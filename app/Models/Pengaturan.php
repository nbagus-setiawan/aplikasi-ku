<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturans';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Ambil nilai pengaturan berdasarkan key dengan fallback nilai default.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (Throwable) {
            return $default;
        }
    }

    /**
     * Simpan atau perbarui nilai pengaturan berdasarkan key.
     */
    public static function set(string $key, ?string $value): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Ambil semua pengaturan dalam bentuk array key-value.
     */
    public static function getAllKeyValues(): array
    {
        try {
            return static::pluck('value', 'key')->toArray();
        } catch (Throwable) {
            return [];
        }
    }
}
