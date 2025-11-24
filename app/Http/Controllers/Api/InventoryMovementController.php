<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryMovement;
use App\Models\Product;

class InventoryMovementController extends Controller
{
    // Listar todos los movimientos
    public function index()
    {
        $movements = InventoryMovement::with(['product', 'warehouse'])->get();

        return response()->json([
            'movements' => $movements
        ]);
    }

    // Crear un nuevo movimiento
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:entrada,salida',
            'note' => 'nullable|string',
        ]);

        $movement = InventoryMovement::create($request->all());

        // Actualizar stock del producto
        $product = Product::find($movement->product_id);
        if ($movement->type === 'entrada') {
            $product->stock += $movement->quantity;
        } else {
            $product->stock -= $movement->quantity;
        }
        $product->save();

        return response()->json([
            'message' => 'Movimiento registrado exitosamente',
            'movement' => $movement
        ], 201);
    }
}
