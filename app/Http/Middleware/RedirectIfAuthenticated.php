<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Jika user sudah login, arahkan sesuai role
                $user = Auth::guard($guard)->user();

                if ($user->role === 'admin') {
                    return redirect()->route('home');
                }

                if ($user->role === 'user') {
                    return redirect()->route('user.dashboard');
                }

                // Fallback ke default jika role tidak teridentifikasi
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
