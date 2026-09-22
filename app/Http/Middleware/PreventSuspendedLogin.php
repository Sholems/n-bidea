<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventSuspendedLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::attempt($credentials = $request->only('email', 'password'))) {
            $user = Auth::user();

            if ($user->account_status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account has been suspended. Please contact an administrator.',
                ]);
            }
        }

        return $next($request);
    }
}
