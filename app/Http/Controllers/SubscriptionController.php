<?php

// app/Http/Controllers/SubscriptionController.php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Subscription::with([
            'tenant',
            'plan'
        ])->latest()->get();
    }

    public function store(Request $request)
    {
        return Subscription::create($request->all());
    }

    public function show(Subscription $subscription)
    {
        return $subscription->load([
            'tenant',
            'plan',
            'invoices'
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        $subscription->update($request->all());

        return $subscription;
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
