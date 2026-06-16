<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $user = $request->user();
        
        if (!$user->role) {
            return redirect('/dashboard')->with('error', 'Unauthorized access.');
        }

        if (!in_array($user->role->name, $roles)) {
            return redirect('/dashboard')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
