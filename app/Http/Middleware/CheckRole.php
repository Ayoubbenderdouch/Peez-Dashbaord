<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Checks if the authenticated user has the required role.
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Required role (e.g., 'vendor', 'admin', 'manager')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Ensure user is authenticated
        if (!$request->user()) {
            return response()->json([
                'type' => 'https://peez.dz/errors/unauthorized',
                'title' => 'Unauthorized',
                'status' => 401,
                'detail' => 'Authentication required.',
                'code' => 'UNAUTHENTICATED',
            ], 401);
        }

        // Check if user has the required role
        if ($request->user()->role !== $role) {
            return response()->json([
                'type' => 'https://peez.dz/errors/forbidden',
                'title' => 'Forbidden',
                'status' => 403,
                'detail' => "Access denied. This endpoint requires '{$role}' role.",
                'code' => 'INSUFFICIENT_PERMISSIONS',
            ], 403);
        }

        return $next($request);
    }
}

