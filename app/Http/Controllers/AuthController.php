<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/overview');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required',
            'password' => 'required',
        ]);

        $loginInput = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Coba login menggunakan email
        if (Auth::attempt(['email' => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/overview');
        }

        // Coba login menggunakan username (apapun formatnya, termasuk format email)
        if (Auth::attempt(['username' => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/overview');
        }

        return back()->withErrors([
            'login' => 'Username/Email atau password salah.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
