<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if ($setting) {
            $decoded = json_decode($setting->value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return $setting->value;
        }
        return $default;
    }

    public static function set($key, $value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
