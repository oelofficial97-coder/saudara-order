<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user()?->fresh();
        if (! $user || ! $user->is_active || ! in_array($user->role, ['pemilik', 'pengelola'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors(['username' => 'Akses admin tidak tersedia. Silakan masuk dengan akun aktif.']);
        }
        Auth::setUser($user);

        return $next($request);
    }
}
