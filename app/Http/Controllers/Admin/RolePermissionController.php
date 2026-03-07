<?php

// File: app/Http/Controllers/Admin/RolePermissionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    /**
     * Show the permission matrix.
     * Groups all permissions and marks which ones cashier currently has.
     */
    public function index()
    {
        // All permissions grouped by their group name
        $permissions = DB::table('permissions')
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        // Keys currently granted to cashier — flipped for fast isset() lookup
        $granted = DB::table('role_permissions')
            ->where('role', 'cashier')
            ->pluck('permission_key')
            ->flip()
            ->all();

        return view('admin.permissions.index', compact('permissions', 'granted'));
    }

    /**
     * Save the full permission set for the cashier role.
     * Deletes all existing, then inserts the checked ones fresh.
     */
    public function update(Request $request)
    {
        $checked = $request->input('permissions', []);

        // Only allow keys that actually exist in the permissions table
        $validKeys = DB::table('permissions')->pluck('key')->all();
        $checked   = array_values(array_intersect($checked, $validKeys));

        DB::transaction(function () use ($checked) {

            // Wipe all current cashier permissions
            DB::table('role_permissions')
                ->where('role', 'cashier')
                ->delete();

            // Insert the newly checked ones
            if (! empty($checked)) {
                $rows = array_map(fn ($key) => [
                    'role'           => 'cashier',
                    'permission_key' => $key,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ], $checked);

                DB::table('role_permissions')->insert($rows);
            }
        });

        // Bust the cache so all cashier sessions pick up changes immediately
        User::clearPermissionCache('cashier');

        return back()->with('success', 'Cashier permissions updated successfully.');
    }
}