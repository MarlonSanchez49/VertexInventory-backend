<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Mesa::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:mesas',
            'status' => 'string',
        ]);

        $mesa = Mesa::create($request->all());

        return response()->json($mesa, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Mesa $mesa)
    {
        return $mesa;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mesa $mesa)
    {
        $request->validate([
            'name' => 'string|unique:mesas,name,' . $mesa->id,
            'status' => 'string',
        ]);

        $mesa->update($request->all());

        return response()->json($mesa, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mesa $mesa)
    {
        $mesa->delete();

        return response()->json(null, 204);
    }
}
