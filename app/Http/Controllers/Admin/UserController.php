<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // ✅ After — only admin + cashier
        $users = User::whereIn('role', ['admin', 'cashier'])->latest()->paginate(10);
        // $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role'     => ['required', Rule::in(['admin', 'cashier'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'cashier'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function permissions(User $user)
    {
        $allPermissions = DB::table('permissions')
            ->orderBy('group')->orderBy('key')
            ->get()
            ->groupBy('group');

        $rolePermissions = DB::table('role_permissions')
            ->where('role', $user->role)
            ->pluck('permission_key', 'permission_key');

        $userOverrides = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('permission_key');

        return view('admin.users.permissions', compact(
            'user', 'allPermissions', 'rolePermissions', 'userOverrides'
        ));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $validKeys = DB::table('permissions')->pluck('key', 'key');
        $overrides = $request->input('overrides', []); // ['key' => 'allow'|'deny'|'inherit']

        DB::transaction(function () use ($user, $overrides, $validKeys) {
            foreach ($overrides as $key => $value) {
                if (!$validKeys->has($key)) continue;

                if ($value === 'inherit') {
                    // Remove override — fall back to role
                    DB::table('user_permissions')
                        ->where('user_id', $user->id)
                        ->where('permission_key', $key)
                        ->delete();
                } else {
                    DB::table('user_permissions')->updateOrInsert(
                        ['user_id' => $user->id, 'permission_key' => $key],
                        ['granted' => $value === 'allow', 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        });

        // Clear any cached role permissions (user overrides are not cached)
        User::clearPermissionCache($user->role);

        return back()->with('success', 'Permissions updated for ' . $user->name);
    }
}