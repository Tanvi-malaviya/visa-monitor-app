<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): static
    {
        $encodedValue = match ($type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => json_encode($value),
            default => (string) $value,
        };

        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $encodedValue;
        $setting->type = $type;
        if ($description !== null || ! $setting->exists) {
            $setting->description = $description ?? $setting->description ?? '';
        }
        $setting->save();

        return $setting;
    }
}
