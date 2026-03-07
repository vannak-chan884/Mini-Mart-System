<?php

// File: app/Traits/HasPermissions.php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait HasPermissions
{
    /**
     * Check if this user has a given permission key.
     *
     * - Admin role  → always true, no DB query.
     * - Other roles → checked against role_permissions table (cached).
     *
     * Usage:
     *   auth()->user()->canDo('expenses.create')
     */
    public function canDo(string $key): bool
    {
        // Admin bypasses every check
        if ($this->role === 'admin') {
            return true;
        }

        return $this->cachedPermissions()->contains($key);
    }

    /**
     * Return all permission keys for this user's role.
     * Cached for 10 minutes per role to avoid repeated DB hits.
     */
    public function cachedPermissions(): \Illuminate\Support\Collection
    {
        return Cache::remember(
            "role_permissions_{$this->role}",
            now()->addMinutes(10),
            fn () => DB::table('role_permissions')
                ->where('role', $this->role)
                ->pluck('permission_key')
        );
    }

    /**
     * Clear the permission cache for a role.
     * Call this after saving changes in the permissions UI.
     *
     * Usage:
     *   User::clearPermissionCache('cashier');
     */
    public static function clearPermissionCache(string $role): void
    {
        Cache::forget("role_permissions_{$role}");
    }
}