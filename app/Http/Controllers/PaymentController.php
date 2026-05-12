<?php

// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return Payment::with([
            'order',
            'user'
        ])->latest()->get();
    }

    public function store(Request $request)
    {
        return Payment::create($request->all());
    }

    public function show(Payment $payment)
    {
        return $payment->load([
            'order',
            'user'
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $payment->update($request->all());

        return $payment;
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
