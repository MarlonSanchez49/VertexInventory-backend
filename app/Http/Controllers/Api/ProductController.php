<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    // LISTAR PRODUCTOS
    public function index()
    {
        return response()->json([
            'message' => 'Lista de productos',
            'data' => Product::with('category')->get()
        ], 200);
    }

    // CREAR PRODUCTO
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'status' => 'required|in:available,not available',
                'category_id' => 'nullable|exists:categories,id'
            ]);

            $product = Product::create($request->all());

            return response()->json([
                'message' => 'Producto creado correctamente',
                'data' => Product::with('category')->find($product->id)
            ], 201);
        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // MOSTRAR UN PRODUCTO
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => $product
        ], 200);
    }

    // ACTUALIZAR PRODUCTO
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'stock' => 'sometimes|integer|min:0',
                'status' => 'sometimes|in:available,not available',
                'category_id' => 'nullable|exists:categories,id'
            ]);

            $product->update($request->all());

            return response()->json([
                'message' => 'Producto actualizado correctamente',
                'data' => Product::with('category')->find($product->id)
            ]);
        } catch (ValidationException $e) {

            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // ELIMINAR PRODUCTO
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ]);
    }
}
