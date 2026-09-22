<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $table = 'company_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];

    public static function getVal(string $key, string $default = ''): string
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? ($setting->setting_value ?? $default) : $default;
    }
}
