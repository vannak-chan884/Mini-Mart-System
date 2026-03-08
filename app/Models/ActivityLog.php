<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs'; // ✅ add this explicitly

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // ── Relationships ─────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'System',
        ]);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ───────────────────────────────────────────────
    public function actionBadgeColor(): string
    {
        return match ($this->action) {
            'login'    => 'blue',
            'logout'   => 'gray',
            'created'  => 'green',
            'updated'  => 'amber',
            'deleted'  => 'red',
            'checkout' => 'purple',
            default    => 'gray',
        };
    }
}