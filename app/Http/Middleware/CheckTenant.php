<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->tenant_id === null) {
            // Jika belum punya tenant dan tidak sedang di halaman input tenant, redirect.
            if (!$request->routeIs('tenants.*')) {
                return redirect()->route('tenants.create');
            }
        }
        return $next($request);
    }
}
