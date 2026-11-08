<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        Gate::define('manage-batches', fn (User $user) => $user->is_active && $user->role === 'pemilik');
        RateLimiter::for('admin-login', function (Request $request) {
            $username = $request->input('username');

            return [
                Limit::perMinute(30)->by('admin-ip:'.$request->ip()),
                Limit::perMinute(5)->by('admin-login:'.hash('sha256', (is_string($username) ? Str::lower(trim($username)) : '').'|'.$request->ip())),
            ];
        });
    }
}
