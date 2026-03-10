<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiController
{
    // GET /api/users
    public function index(Request $request)
    {
        $this->requirePermission($request, 'users.view');
        $users = User::latest()->paginate($request->get('per_page', 15));
        return response()->json([
            'data'       => $users->items(),
            'pagination' => [
                'total'        => $users->total(),
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
            ],
        ]);
    }

    // GET /api/users/{id}
    public function show(Request $request, User $user)
    {
        $this->requirePermission($request, 'users.view');
        return response()->json(['data' => $user]);
    }

    // POST /api/users
    public function store(Request $request)
    {
        $this->requirePermission($request, 'users.create');
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,cashier',
        ]);
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        return response()->json(['success' => true, 'data' => $user], 201);
    }

    // PUT /api/users/{id}
    public function update(Request $request, User $user)
    {
        $this->requirePermission($request, 'users.edit');
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role'     => 'required|in:admin,cashier',
        ]);
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $user->update($validated);
        return response()->json(['success' => true, 'data' => $user->fresh()]);
    }

    // DELETE /api/users/{id}
    public function destroy(Request $request, User $user)
    {
        $this->requirePermission($request, 'users.delete');
        if ($user->id === $request->user()->id) {
            return response()->json(['error' => 'Cannot delete your own account.'], 422);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }

    // GET /api/users/me/permissions
    public function myPermissions(Request $request)
    {
        return response()->json([
            'role'        => $request->user()->role,
            'permissions' => $request->user()->cachedPermissions(),
        ]);
    }

    private function requirePermission(Request $request, string $permission): void
    {
        if (!$request->user()->canDo($permission)) {
            abort(response()->json(['error' => 'Permission denied.'], 403));
        }
    }

    /**
     * GET /api/users/{id}/permissions
     * Get all permissions for a specific user
     */
    public function permissions(User $user)
    {
        // Get role default permissions
        $rolePermissions = \DB::table('role_permissions')
            ->where('role', $user->role)
            ->pluck('permission_key')
            ->toArray();

        // Get user-specific overrides
        $userPermissions = \DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('permission_key');

        // Build full permission list
        $allKeys = array_unique(array_merge(
            $rolePermissions,
            $userPermissions->keys()->toArray()
        ));

        $permissions = collect($allKeys)->map(function ($key) use ($rolePermissions, $userPermissions) {
            $roleDefault  = in_array($key, $rolePermissions);
            $userOverride = $userPermissions->get($key);

            return [
                'key'          => $key,
                'role_default' => $roleDefault,
                'user_granted' => $userOverride ? (bool) $userOverride->granted : $roleDefault,
                'overridden'   => $userOverride !== null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'User permissions loaded.',
            'data'    => [
                'user'        => $user->only('id', 'name', 'email', 'role'),
                'permissions' => $permissions,
            ],
        ]);
    }

    /**
     * PUT /api/users/{id}/permissions
     * Bulk update user permissions
     *
     * Request body:
     * {
     *   "permissions": {
     *     "products.view": true,
     *     "products.delete": false,
     *     "sales.view": true
     *   }
     * }
     */
    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'boolean',
        ]);

        foreach ($request->permissions as $key => $granted) {
            \DB::table('user_permissions')->updateOrInsert(
                ['user_id' => $user->id, 'permission_key' => $key],
                ['granted' => $granted]
            );
        }

        // Clear permission cache for this user's role
        \Cache::forget("role_permissions_{$user->role}");

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully.',
            'data'    => [
                'user'        => $user->only('id', 'name', 'role'),
                'updated'     => count($request->permissions),
            ],
        ]);
    }
}