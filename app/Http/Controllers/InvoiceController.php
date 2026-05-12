<?php

// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::with([
            'tenant',
            'subscription'
        ])->latest()->get();
    }

    public function store(Request $request)
    {
        return Invoice::create($request->all());
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load([
            'tenant',
            'subscription'
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $invoice->update($request->all());

        return $invoice;
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
