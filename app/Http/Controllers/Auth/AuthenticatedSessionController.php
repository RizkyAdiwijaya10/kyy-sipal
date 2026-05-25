<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
        // Ambil input login (bisa username atau email)
        $login = $request->input('login');
        $password = $request->input('password');

        // Cek apakah input adalah email atau username
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        // Attempt login
        if (Auth::attempt([$fieldType => $login, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            if (auth()->user()->role == 'admin') {
                return redirect()->intended(route('dashboard', absolute: false));
            }
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Jika login gagal
        return back()->withErrors([
            'login' => 'Username/Email atau password salah.',
        ])->onlyInput('login');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
