<?php

// app/Http/Controllers/PlanController.php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return Plan::latest()->get();
    }

    public function store(Request $request)
    {
        return Plan::create($request->all());
    }

    public function show(Plan $plan)
    {
        return $plan;
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($request->all());

        return $plan;
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
