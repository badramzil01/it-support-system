<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];

    public static function getValue($key, $default = null)
    {
        return cache()->remember(
            "setting_{$key}",
            3600,
            fn() => self::where('key', $key)->value('value') ?? $default
        );
    }

    public static function setValue($key, $value)
    {
        cache()->forget("setting_{$key}");

        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
