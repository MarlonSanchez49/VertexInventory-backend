<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployeeController extends Controller
{
    /**
     * Mostrar todos los empleados
     */
    public function index()
    {
        return response()->json([
            'message' => 'Lista de empleados',
            'data' => Employee::all()
        ], 200);
    }

    /**
     * Crear un nuevo empleado
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'       => 'required|string|max:255',
                'position'   => 'required|string|max:255',
                'phone'      => 'nullable|string|max:20',
                'email'      => 'nullable|email|max:255',
                'status'     => 'required|in:active,inactive',
            ]);

            $employee = Employee::create($request->all());

            return response()->json([
                'message' => 'Empleado creado correctamente',
                'data' => $employee
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Mostrar un empleado específico
     */
    public function show($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        return response()->json([
            'data' => $employee
        ], 200);
    }

    /**
     * Actualizar empleado
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        try {
            $request->validate([
                'name'       => 'sometimes|string|max:255',
                'position'   => 'sometimes|string|max:255',
                'phone'      => 'sometimes|string|max:20',
                'email'      => 'sometimes|email|max:255',
                'status'     => 'sometimes|in:active,inactive',
            ]);

            $employee->update($request->all());

            return response()->json([
                'message' => 'Empleado actualizado correctamente',
                'data' => $employee
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Eliminar empleado
     */
    public function destroy($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json([
                'message' => 'Empleado no encontrado'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'message' => 'Empleado eliminado correctamente'
        ]);
    }
}
