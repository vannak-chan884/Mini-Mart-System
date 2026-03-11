<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    // POST /api/auth/login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens if you want single-session
        // $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user'  => new \App\Http\Resources\UserResource($user),
        ]);
    }

    // POST /api/auth/register
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => \Hash::make($request->password),
            'role'     => 'customer',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        // ✅ Same structure as login
        return $this->created([
            'token' => $token,
            'user'  => new \App\Http\Resources\UserResource($user),
        ], 'Registered successfully.');
    }

    // POST /api/auth/logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logged out successfully.']);
    }

    // GET /api/auth/me
    public function me(Request $request)
    {
        $user = $request->user();

        return $this->success(new \App\Http\Resources\UserResource($user->load([])));
    }

    // POST /api/auth/refresh  — revoke current token, issue a new one
    public function refresh(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['success' => true, 'token' => $token]);
    }

    /**
     * PUT /api/auth/password
     * Change password and revoke all tokens
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Check current password
        if (!\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        // Update password
        $user->update([
            'password' => \Hash::make($request->password),
        ]);

        // Revoke ALL tokens (force re-login on all devices)
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please login again.',
        ]);
    }

    // DELETE /api/auth/account
    public function deleteAccount(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if (!\Hash::check($request->password, $request->user()->password)) {
            return $this->error('Incorrect password.', 422);
        }

        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return $this->success(null, 'Account deleted successfully.');
    }
}