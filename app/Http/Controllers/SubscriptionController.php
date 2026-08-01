<?php

// app/Http/Controllers/SubscriptionController.php
//
// CATATAN: Controller ini diasumsikan HANYA untuk admin platform GrowPOS
// (bukan tenant/UMKM). Pastikan route-nya dibungkus middleware role admin,
// contoh di routes/web.php:
//
// Route::middleware(['auth', 'role:superadmin'])->group(function () {
//     Route::resource('subscriptions', SubscriptionController::class);
// });
//
// Kalau ternyata tenant JUGA perlu akses (misal lihat status langganan
// sendiri), buat method terpisah khusus tenant (misal `mySubscription()`)
// yang difilter tenant_id, JANGAN pakai controller resource penuh ini.

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Subscription::with(['tenant', 'plan'])->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan_id'   => 'required|exists:plans,id',
            'status'    => 'nullable|string|in:active,inactive,expired',
            'starts_at' => 'required|date',
            'ends_at'   => 'nullable|date|after:starts_at',
        ]);

        return Subscription::create($validated);
    }

    public function show(Subscription $subscription)
    {
        return $subscription->load(['tenant', 'plan', 'invoices']);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'plan_id'   => 'sometimes|exists:plans,id',
            'status'    => 'sometimes|string|in:active,inactive,expired',
            'ends_at'   => 'nullable|date',
        ]);

        $subscription->update($validated);

        return $subscription;
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
