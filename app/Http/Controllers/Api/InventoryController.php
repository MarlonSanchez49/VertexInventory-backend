<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return response()->json(Inventory::with(['warehouse', 'product'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);

        $inventory = Inventory::create($validated);

        return response()->json([
            "message" => "Inventario creado correctamente",
            "data" => $inventory
        ], 201);
    }

    public function show($id)
    {
        $inventory = Inventory::with(['warehouse', 'product'])->find($id);

        if (!$inventory) {
            return response()->json(["message" => "Inventario no encontrado"], 404);
        }

        return response()->json($inventory);
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(["message" => "Inventario no encontrado"], 404);
        }

        $validated = $request->validate([
            'warehouse_id' => 'exists:warehouses,id',
            'product_id' => 'exists:products,id',
            'quantity' => 'integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);

        $inventory->update($validated);

        return response()->json([
            "message" => "Inventario actualizado correctamente",
            "data" => $inventory
        ]);
    }

    public function destroy($id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json(["message" => "Inventario no encontrado"], 404);
        }

        $inventory->delete();

        return response()->json(["message" => "Inventario eliminado correctamente"]);
    }
}
