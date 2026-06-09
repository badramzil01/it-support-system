<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Schema;
use App\Models\Notification as AppNotification;
use App\Models\Ticket;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        $user = auth()->user();

        if ($useLaravel) {
            $query = $user->notifications();
        } else {
            $query = AppNotification::with('ticket');
        }

        // Filters
        $stats = [
            'urgent'    => Ticket::where('is_urgent', true)->count(),
            'escalated' => Ticket::where('is_escalated', true)->count(),
            'errors'    => (clone $query)->where(function($q){ $q->where('type','like','%error%')->orWhere('type','like','%fail%'); })->count() ?? 0,
        ];

        return view('admin.notifications.index', compact('useLaravel','stats','filter'));
    }

    public function markRead($id)
    {
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $n = auth()->user()->notifications()->where('id',$id)->first();
            if (!$n) return response()->json(['error'=>'Not found'],404);
            $n->markAsRead();
            return response()->json(['ok'=>true]);
        }
        $n = AppNotification::find($id);
        if (!$n) return response()->json(['error'=>'Not found'],404);
        $n->status = 'sent';
        $n->save();
        return response()->json(['ok'=>true]);
    }

    public function markAllRead()
    {
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            auth()->user()->unreadNotifications->markAsRead();
        } else {
            AppNotification::where('status','pending')->update(['status'=>'sent']);
        }
        return response()->json(['ok'=>true]);
    }

    public function destroy($id)
    {
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $n = auth()->user()->notifications()->where('id',$id)->first();
        } else {
            $n = AppNotification::find($id);
        }
        if (!$n) return back()->with('error','Notification introuvable');
        $n->delete();
        return back()->with('success','Notification supprimée');
    }

    public function destroyAll()
    {
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) auth()->user()->notifications()->delete();
        else AppNotification::query()->delete();
        return back()->with('success','Toutes les notifications ont été supprimées');
    }

    public function unreadCount()
    {
        $useLaravel = Schema::hasColumn('notifications', 'notifiable_type') && Schema::hasColumn('notifications', 'notifiable_id');
        if ($useLaravel) {
            $notifCount = auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
        } else {
            try { $notifCount = AppNotification::where('status','pending')->count(); }
            catch (\Throwable $e) { $notifCount = 0; }
        }

        $clientMsgCount = \App\Models\Message::where('sender', 'user')->whereNull('read_at')->count();
        $internalMsgCount = \App\Models\InternalMessage::where('receiver_id', auth()->id())->where('is_read', false)->count();
        $urgentTicketsCount = \App\Models\Ticket::where('is_urgent', true)->whereIn('status', ['open', 'in_progress'])->count();
        $escalatedTicketsCount = \App\Models\Ticket::where('is_escalated', true)->whereIn('status', ['open', 'in_progress'])->count();

        return response()->json([
            'unread' => $notifCount + $clientMsgCount + $internalMsgCount,
            'notif_count' => $notifCount,
            'client_messages' => $clientMsgCount,
            'internal_messages' => $internalMsgCount,
            'urgent_tickets' => $urgentTicketsCount,
            'escalated_tickets' => $escalatedTicketsCount,
        ]);
    }
}
