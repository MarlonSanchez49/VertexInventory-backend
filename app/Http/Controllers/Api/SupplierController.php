<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Lista de proveedores',
            'data' => Supplier::all()
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
            ]);

            $supplier = Supplier::create($request->all());

            return response()->json([
                'message' => 'Proveedor creado correctamente',
                'data' => $supplier
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        return response()->json(['data' => $supplier]);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
            ]);

            $supplier->update($request->all());

            return response()->json([
                'message' => 'Proveedor actualizado correctamente',
                'data' => $supplier
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json(['message' => 'Proveedor no encontrado'], 404);
        }

        $supplier->delete();

        return response()->json(['message' => 'Proveedor eliminado correctamente']);
    }
}
