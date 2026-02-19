<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('admin', function ($user) {
            return $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        });

        Gate::define('super_admin', function ($user) {
            return $user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
        });
    }
}
