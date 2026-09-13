<?php

// app/Http/Controllers/ServiceOrderController.php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        return ServiceOrder::create(array_merge($this->validated($request), ['tenant_id' => $request->user()->tenant_id]));
    }

    public function show(ServiceOrder $serviceOrder)
    {
        return $serviceOrder->load('order');
    }

    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        $data = $this->validated($request, false);
        $serviceOrder->update($data);

        return $serviceOrder;
    }

    private function validated(Request $request, bool $creating = true): array
    {
        return $request->validate([
            'order_id' => $creating ? ['required', 'integer', Rule::exists('orders', 'id')->where('tenant_id', $request->user()->tenant_id)] : ['prohibited'],
            'service_status' => 'sometimes|required|in:received,washing,drying,finished,picked_up',
            'estimated_finish' => 'nullable|date',
            'finished_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);
    }

    public function destroy(ServiceOrder $serviceOrder)
    {
        $serviceOrder->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
