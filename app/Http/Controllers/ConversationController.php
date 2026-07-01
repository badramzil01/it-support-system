<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ConversationController extends Controller
{
    /**
     * Delete a conversation and all its messages (with images/tickets).
     */
    public function destroy($id)
    {
        try {
            $conversation = Conversation::findOrFail((int) $id);

            // Security: only the owner can delete
            if ($conversation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action non autorisee.',
                ], 403);
            }

            // Delete all messages with their images
            $messages = Message::where('conversation_id', $conversation->id)->get();
            
            foreach ($messages as $message) {
                // Delete associated image file
                if ($message->image_path) {
                    $this->deleteImageFile($message->image_path);
                }
                
                // Delete the message
                $message->delete();
            }

            // Delete associated tickets
            $conversation->tickets()->delete();

            // Delete the conversation
            $conversation->delete();

            Log::info('conversation.deleted', [
                'user_id'         => Auth::id(),
                'conversation_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation supprimee avec succes.',
            ]);

        } catch (\Throwable $e) {
            Log::error('conversation.delete.error', [
                'message' => $e->getMessage(),
                'conversation_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la conversation.',
            ], 500);
        }
    }

    /**
     * Delete a single message.
     */
    public function deleteMessage($id)
    {
        try {
            $message = Message::findOrFail((int) $id);

            // Security: only the owner can delete
            if ($message->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action non autorisee.',
                ], 403);
            }

            // Delete associated image file
            if ($message->image_path) {
                $this->deleteImageFile($message->image_path);
            }

            $message->delete();

            Log::info('message.deleted', [
                'user_id'    => Auth::id(),
                'message_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message supprime.',
            ]);

        } catch (\Throwable $e) {
            Log::error('message.delete.error', [
                'message' => $e->getMessage(),
                'message_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du message.',
            ], 500);
        }
    }

    /**
     * Update a message content.
     */
    public function updateMessage(Request $request, $id)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:10000',
            ]);

            $message = Message::findOrFail((int) $id);

            // Security: only the owner can edit
            if ($message->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action non autorisee.',
                ], 403);
            }

            // Only allow editing user messages
            if ($message->sender !== 'user') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seuls les messages utilisateur peuvent etre modifies.',
                ], 403);
            }

            $message->update([
                'content'   => $request->input('content'),
                'is_edited' => true,
                'edited_at' => now(),
            ]);

            Log::info('message.edited', [
                'user_id'    => Auth::id(),
                'message_id' => $id,
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'Message modifie.',
                'content'    => $message->content,
                'is_edited'  => true,
                'edited_at'  => $message->edited_at->toISOString(),
            ]);

        } catch (\Throwable $e) {
            Log::error('message.update.error', [
                'message' => $e->getMessage(),
                'message_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du message.',
            ], 500);
        }
    }

    /**
     * Delete image file from storage.
     */
    private function deleteImageFile(?string $imagePath): void
    {
        if (!$imagePath) return;

        try {
            // Extract the relative path from the URL
            // e.g. http://localhost:8000/storage/support-images/file.png → support-images/file.png
            // e.g. /storage/support-images/file.png → support-images/file.png
            $relativePath = $imagePath;
            
            if (str_contains($relativePath, '/storage/')) {
                $relativePath = substr($relativePath, strpos($relativePath, '/storage/') + 9);
            } elseif (str_contains($relativePath, 'storage/')) {
                $relativePath = substr($relativePath, strpos($relativePath, 'storage/') + 8);
            }
            
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
                Log::info('image.deleted', ['path' => $relativePath]);
            }
        } catch (\Throwable $e) {
            Log::warning('image.delete.error', [
                'path'  => $imagePath,
                'error' => $e->getMessage(),
            ]);
        }
    }
}