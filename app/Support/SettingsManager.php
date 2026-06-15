<?php

namespace App\Support;

use App\Models\Setting;

class SettingsManager
{
    public static function get($key, $env = null)
    {
        return Setting::getValue(
            $key,
            $env ? env($env) : null
        );
    }
}
