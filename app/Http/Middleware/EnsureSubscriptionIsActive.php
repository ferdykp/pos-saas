<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->tenant) {
            $tenant = $user->tenant;

            // Cek apakah tenant memiliki paket aktif yang belum kadaluarsa
            $hasActiveSubscription = $tenant->subscriptions()
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->exists();

            // Jika tidak ada paket aktif / sudah habis masa berlakunya
            if (!$hasActiveSubscription) {
                // Kecualikan rute billing, setup bisnis, dan logout agar pengguna tetap bisa bayar
                if ($request->routeIs('billing.*') || $request->routeIs('tenants.*') || $request->is('logout')) {
                    return $next($request);
                }

                return redirect()->route('billing.index')
                    ->with('warning', 'Masa berlangganan Anda telah habis atau belum aktif. Silakan pilih paket langganan untuk melanjutkan.');
            }
        }

        return $next($request);
    }
}
