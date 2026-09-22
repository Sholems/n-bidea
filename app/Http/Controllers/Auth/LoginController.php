<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->withInput($request->only('email', 'remember'));
        }

        if ($user->account_status !== 'active') {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Your account has been suspended. Please contact support.'])
                ->withInput($request->only('email', 'remember'));
        }

        $request->session()->regenerate();

        Log::info('User logged in', ['user_id' => $user->id, 'role' => $user->role]);

        return redirect()->route($user->dashboardRoute())
            ->with('success', 'Welcome back, '.$user->name.'!');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('home');
    }
}
