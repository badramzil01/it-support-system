<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('ticket')->latest()->paginate(15);

        return view('notifications',compact('notifications'));
    }
}
