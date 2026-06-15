<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class RuntimeSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');

        return response()->json($settings);
    }
}