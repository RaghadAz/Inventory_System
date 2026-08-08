<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }
        
        $userRole = auth()->user()->role;
        
        if ($role === 'admin' && $userRole !== 'admin') {
            abort(403, 'Unauthorized');
        }
        
        if ($role === 'cashier' && !in_array($userRole, ['admin', 'cashier'])) {
            abort(403, 'Unauthorized');
        }
        
        return $next($request);
    }
}