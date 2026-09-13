<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;

class StockMovementController extends Controller
{
    public function index()
    {
        return StockMovement::with(['product', 'material', 'user'])->latest()->paginate(20);
    }
}
