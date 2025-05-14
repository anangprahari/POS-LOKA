<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Default redirect (tidak dipakai karena kita override method `authenticated`)
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle post-login redirection based on role.
     */
    protected function authenticated($request, $user)
    {
        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        if ($user->role === 'user') {
            return redirect()->intended('/user');
        }

        // Jika role tidak dikenali, logout dan tolak akses
        Auth::logout();
        return redirect('/login')->withErrors(['email' => 'Role tidak dikenali.']);
    }
}
