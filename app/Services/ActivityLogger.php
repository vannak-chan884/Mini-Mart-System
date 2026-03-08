<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        array $properties = []
    ): void {
        try {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->id,
                'properties' => empty($properties) ? null : $properties,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // Never let logging crash the app
            \Illuminate\Support\Facades\Log::warning('ActivityLogger failed: ' . $e->getMessage());
        }
    }

    // ── Convenience wrappers ──────────────────────────────────

    public static function created(string $module, Model $subject, string $description = ''): void
    {
        static::log('created', $module, $description ?: "{$module} created", $subject);
    }

    public static function updated(string $module, Model $subject, array $changes = []): void
    {
        static::log('updated', $module, "{$module} updated", $subject, $changes);
    }

    public static function deleted(string $module, string $description, ?Model $subject = null): void
    {
        static::log('deleted', $module, $description, $subject);
    }

    public static function login(): void
    {
        static::log('login', 'Auth', (auth()->user()?->name ?? 'Unknown') . ' logged in');
    }

    public static function logout(): void
    {
        // Must be called BEFORE Auth::logout() in your controller
        $name = auth()->user()?->name ?? 'Unknown';
        static::log('logout', 'Auth', $name . ' logged out');
    }
}