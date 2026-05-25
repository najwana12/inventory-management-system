<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = Auth::user()->role->name;

        // role yang boleh akses
        if (
            $role != 'admin' &&
            $role != 'super_admin' &&
            $role != 'staff' &&
            $role != 'sales' &&
            $role != 'pembelian'
        ) {

            return redirect()->back()
                ->with('error', 'Akses ditolak');
        }

        return $next($request);
    }
}