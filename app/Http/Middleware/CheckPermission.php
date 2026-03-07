<?php

// File: app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * Usage on routes:
     *   ->middleware('permission:expenses.create')
     *
     * Usage in controller constructor:
     *   $this->middleware('permission:expenses.create')->only(['create', 'store']);
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->canDo($permission)) {

            // API / AJAX request → return JSON 403
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'You do not have permission to perform this action.',
                ], 403);
            }

            // Web request → abort with 403 page
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}