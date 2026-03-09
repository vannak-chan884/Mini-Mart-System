<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\ActivityLogger;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // In store() after Auth::login():
        ActivityLogger::login();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ✅ Capture before ANYTHING is cleared
        $userId = auth()->id();
        $name   = auth()->user()?->name ?? 'Unknown';

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Log AFTER logout but pass userId explicitly so auth() null doesn't matter
        \App\Models\ActivityLog::create([
            'user_id'     => $userId,
            'action'      => 'logout',
            'module'      => 'Auth',
            'description' => "{$name} logged out",
            'ip_address'  => $request->ip(),
        ]);

        return redirect('/');
    }
}
