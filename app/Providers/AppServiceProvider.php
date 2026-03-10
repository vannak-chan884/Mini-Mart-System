<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        // ── @canDo('permission.key') ──────────────────────────────
        //
        // Show content only if the logged-in user HAS the permission.
        //
        // Example:
        //   @canDo('expenses.create')
        //       <a href="{{ route('admin.expenses.create') }}">Add Expense</a>
        //   @endCanDo
        //
        Blade::directive('canDo', function (string $expression) {
            return "<?php if (auth()->check() && auth()->user()->canDo({$expression})): ?>";
        });

        Blade::directive('endCanDo', function () {
            return '<?php endif; ?>';
        });

        // ── @cannotDo('permission.key') ───────────────────────────
        //
        // Show content only if the logged-in user does NOT have the permission.
        //
        // Example:
        //   @cannotDo('expenses.create')
        //       <p>You don't have access to create expenses.</p>
        //   @endCannotDo
        //
        Blade::directive('cannotDo', function (string $expression) {
            return "<?php if (! auth()->check() || ! auth()->user()->canDo({$expression})): ?>";
        });

        Blade::directive('endCannotDo', function () {
            return '<?php endif; ?>';
        });

        Paginator::useTailwind(); // ← or useBootstrap() if you use Bootstrap

        // Only admin can view API docs
        // Protect API docs — only admin can view
        Gate::define('viewApiDocs', function ($user) {
            return $user->role === 'admin';
        });

        Scramble::afterOpenApiGenerated(function () {});
    }
}
