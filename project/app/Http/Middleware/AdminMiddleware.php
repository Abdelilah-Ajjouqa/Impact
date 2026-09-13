<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, String $role)
    {
        if (!Auth::check()) {
            return redirect()
                ->route('auth.login.form')
                ->with('error', 'You need to log in first.');
        }

        if (!$request->user()->getRole($role)) {
            return redirect()
                ->route('posts.index')
                ->with('error', 'You do not have access to this page.');
        }

        return $next($request);
    }
}
