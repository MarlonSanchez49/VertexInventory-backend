<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Venta::with(['employee', 'mesa', 'metodoPago', 'products'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'mesa_id' => 'required|exists:mesas,id',
            'metodo_pago_id' => 'required|exists:metodo_pagos,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;
        $products = [];

        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            if ($product->stock < $productData['quantity']) {
                return response()->json(['message' => 'Not enough stock for product ' . $product->name], 400);
            }
            $total += $product->price * $productData['quantity'];
            $products[] = [
                'product' => $product,
                'quantity' => $productData['quantity'],
            ];
        }

        try {
            DB::beginTransaction();

            $venta = Venta::create([
                'employee_id' => $request->employee_id,
                'mesa_id' => $request->mesa_id,
                'metodo_pago_id' => $request->metodo_pago_id,
                'total' => $total,
                'status' => 'completed',
            ]);

            foreach ($products as $productData) {
                $product = $productData['product'];
                $quantity = $productData['quantity'];
                $venta->products()->attach($product->id, ['quantity' => $quantity, 'price' => $product->price]);
                $product->decrement('stock', $quantity);
            }

            DB::commit();

            return response()->json($venta->load(['employee', 'mesa', 'metodoPago', 'products']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating sale: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        return $venta->load(['employee', 'mesa', 'metodoPago', 'products']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        //
    }
}
