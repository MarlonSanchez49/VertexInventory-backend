<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WarehouseController extends Controller
{
    // LISTAR BODEGAS
    public function index()
    {
        return response()->json([
            'message' => 'Lista de bodegas obtenida correctamente.',
            'data' => Warehouse::all()
        ], 200);
    }

    // CREAR BODEGA
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'capacity' => 'required|integer|min:0',
            ]);

            $warehouse = Warehouse::create($request->all());

            return response()->json([
                'message' => 'Bodega creada correctamente.',
                'data' => $warehouse
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // MOSTRAR UNA BODEGA
    public function show($id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json([
                'message' => 'Bodega no encontrada.'
            ], 404);
        }

        return response()->json([
            'message' => 'Bodega encontrada.',
            'data' => $warehouse
        ], 200);
    }

    // ACTUALIZAR BODEGA
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json([
                'message' => 'Bodega no encontrada.'
            ], 404);
        }

        try {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'location' => 'sometimes|string|max:255',
                'capacity' => 'sometimes|integer|min:0',
            ]);

            $warehouse->update($request->all());

            return response()->json([
                'message' => 'Bodega actualizada correctamente.',
                'data' => $warehouse
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    // ELIMINAR BODEGA
    public function destroy($id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json([
                'message' => 'Bodega no encontrada.'
            ], 404);
        }

        $warehouse->delete();

        return response()->json([
            'message' => 'Bodega eliminada correctamente.'
        ], 200);
    }
}
