<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tenés permisos para acceder a esta sección.'], 403);
            }
            return redirect()->route('products.index')
                ->with('error', 'No tenés permisos para acceder a esa sección.');
        }

        return $next($request);
    }
}
