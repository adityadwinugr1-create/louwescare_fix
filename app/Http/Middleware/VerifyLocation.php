<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class VerifyLocation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Bypass untuk owner
        if ($user && $user->role === 'owner') {
            return $next($request);
        }
        
        // Cek apakah role dibatasi
        $restrictedRoles = Config::get('geo.restricted_roles', ['admin']);
        if (!in_array($user->role ?? '', $restrictedRoles)) {
            return $next($request);
        }
        
        // Cek session location_verified
        if (!$request->session()->has('location_verified')) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Verifikasi lokasi diperlukan untuk role ' . $user->role);
        }
        
        return $next($request);
    }
}

