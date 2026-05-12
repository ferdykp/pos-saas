<?php

// app/Http/Controllers/ServiceOrderController.php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function index()
    {
        return ServiceOrder::with('order')
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        return ServiceOrder::create($request->all());
    }

    public function show(ServiceOrder $serviceOrder)
    {
        return $serviceOrder->load('order');
    }

    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        $serviceOrder->update($request->all());

        return $serviceOrder;
    }

    public function destroy(ServiceOrder $serviceOrder)
    {
        $serviceOrder->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
