<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return MetodoPago::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:metodo_pagos',
        ]);

        $metodoPago = MetodoPago::create($request->all());

        return response()->json($metodoPago, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MetodoPago $metodoPago)
    {
        return $metodoPago;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetodoPago $metodoPago)
    {
        $request->validate([
            'name' => 'string|unique:metodo_pagos,name,' . $metodoPago->id,
        ]);

        $metodoPago->update($request->all());

        return response()->json($metodoPago, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetodoPago $metodoPago)
    {
        $metodoPago->delete();

        return response()->json(null, 204);
    }
}
