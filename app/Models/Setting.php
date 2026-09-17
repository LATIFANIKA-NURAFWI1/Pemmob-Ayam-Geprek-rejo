<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // ─── Static helpers ───────────────────────────────────────────────────────

    /**
     * Retrieve a setting value by key.
     *
     * @param  string  $key
     * @param  mixed   $default  Returned when the key does not exist.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Persist a setting value (insert or update).
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return static
     */
    public static function set(string $key, mixed $value): static
    {
        /** @var static $setting */
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();

        return $setting;
    }
}
