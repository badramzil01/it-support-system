<?php

namespace App\Http\Controllers\SupportUI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Schema;
use App\Models\Notification as AppNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(30);
        } else {
            $notifications = AppNotification::with('ticket')->orderBy('created_at', 'desc')->paginate(30);
        }
        return view('support.notifications.index', compact('notifications', 'useLaravel'));
    }

    public function markRead($id)
    {
        $user = auth()->user();
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $notif = $user->notifications()->where('id', $id)->first();
            if (! $notif) return response()->json(['error' => 'Not found'], 404);
            $notif->markAsRead();
            return response()->json(['ok' => true]);
        }
        $notif = AppNotification::find($id);
        if (! $notif) return response()->json(['error' => 'Not found'], 404);
        $notif->status = 'sent';
        $notif->save();
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        $user = auth()->user();
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $user->unreadNotifications->markAsRead();
            return response()->json(['ok' => true]);
        }
        AppNotification::where('status', 'pending')->update(['status' => 'sent']);
        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $user = auth()->user();
            $notif = $user->notifications()->where('id', $id)->first();
            if (! $notif) return redirect()->route('support.ui.notifications.index')->with('error', 'Notification introuvable');
            $notif->delete();
            return redirect()->route('support.ui.notifications.index')->with('success', 'Notification supprimée');
        }
        $notif = AppNotification::find($id);
        if (! $notif) return redirect()->route('support.ui.notifications.index')->with('error', 'Notification introuvable');
        $notif->delete();
        return redirect()->route('support.ui.notifications.index')->with('success', 'Notification supprimée');
    }

    public function destroyAll()
    {
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $user = auth()->user();
            $user->notifications()->delete();
            if (request()->expectsJson()) {
                return response()->json(['ok' => true]);
            }
            return redirect()->route('support.ui.notifications.index')->with('success', 'Toutes les notifications ont été supprimées');
        }
        AppNotification::query()->delete();
        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('support.ui.notifications.index')->with('success', 'Toutes les notifications ont été supprimées');
    }

    // Return unread notifications count for the current user (or app notifications)
    public function unreadCount()
    {
        $user = auth()->user();
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $count = $user ? $user->unreadNotifications()->count() : 0;
        } else {
            try {
                $count = AppNotification::where('status', 'pending')->count();
            } catch (\Throwable $e) {
                $count = 0;
            }
        }
        return response()->json(['unread' => $count]);
    }
}
