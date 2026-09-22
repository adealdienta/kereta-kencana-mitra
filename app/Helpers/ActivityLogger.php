<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $action, ?string $details = null): void
    {
        try {
            $user = Auth::user();
            ActivityLog::create([
                'user_id' => $user?->id,
                'user_nama' => $user?->name ?? 'Tamu / Sistem',
                'role' => $user?->role ?? 'guest',
                'action' => $action,
                'details' => $details,
                'ip_address' => Request::ip(),
            ]);
        } catch (\Exception $e) {
            // Prevent logging errors from crashing main transactions
        }
    }
}
