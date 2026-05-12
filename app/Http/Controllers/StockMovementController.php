<?php

// app/Http/Controllers/StockMovementController.php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index()
    {
        return StockMovement::with([
            'product',
            'user'
        ])->latest()->get();
    }

    public function store(Request $request)
    {
        return StockMovement::create($request->all());
    }

    public function show(StockMovement $stockMovement)
    {
        return $stockMovement->load([
            'product',
            'user'
        ]);
    }

    public function update(Request $request, StockMovement $stockMovement)
    {
        $stockMovement->update($request->all());

        return $stockMovement;
    }

    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
