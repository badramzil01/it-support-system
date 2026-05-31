<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class LogController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')->latest();
        
        return view('admin.logs', compact('logs'));
    }
    
    public function download()
    {
        $file = storage_path('logs/laravel.log');

        if (!file_exists($file)) {
            abort(404, 'Log file not found');
        }

        return response()->download(
            $file,
            'laravel-log.txt'
        );
    }
}
