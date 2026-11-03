<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Sesi login sudah berakhir. Silakan masuk kembali.');
        }

        if ($user->role !== $role) {
            $dashboard = $user->role === 'partner' ? 'partner.home' : 'customer.home';
            $message = $user->role === 'partner'
                ? 'Halaman ini hanya untuk customer. Sesi browser ini sedang memakai akun partner.'
                : 'Halaman ini hanya untuk partner. Sesi browser ini sedang memakai akun customer.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message, 'redirect' => route($dashboard)], 403);
            }

            if ($request->isMethod('GET')) {
                return redirect()->route($dashboard)->with('role_notice', $message);
            }

            return redirect()->route($dashboard)->with('role_notice', $message.' Perubahan tidak disimpan.');
        }

        if ($role === 'partner' && !$user->store?->starter_catalog_initialized_at) {
            app(\App\Services\PartnerStarterCatalog::class)->initialize($user);
        }
        return $next($request);
    }
}
