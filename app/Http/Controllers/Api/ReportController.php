<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // 1️⃣ Valor total del inventario
    public function inventoryValue()
    {
        $products = Product::select(
            'id',
            'name',
            'price',
            'stock',
            DB::raw('price * stock as total_value')
        )->get();

        $totalInventoryValue = $products->sum('total_value');

        return response()->json([
            'total_value' => $totalInventoryValue,
            'products' => $products
        ]);
    }

    // 2️⃣ Productos más movidos (entradas + salidas)
    public function topProducts()
    {
        $top = InventoryMovement::select(
            'product_id',
            DB::raw('SUM(quantity) as total_moved')
        )
            ->groupBy('product_id')
            ->orderByDesc('total_moved')
            ->with('product')
            ->take(5)
            ->get();

        return response()->json($top);
    }

    // 3️⃣ Productos con bajo stock (<10 unidades)
    public function lowStock()
    {
        $products = Product::where('stock', '<', 10)->get();

        return response()->json([
            'count' => $products->count(),
            'products' => $products
        ]);
    }

    // 4️⃣ Reporte: Entradas vs Salidas por mes (para gráficas)
    public function movementsByMonth()
    {
        $data = InventoryMovement::select(
            // CAMBIO AQUÍ: Usamos DATE_FORMAT para MySQL
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(CASE WHEN type = "entrada" THEN quantity ELSE 0 END) as total_in'),
            DB::raw('SUM(CASE WHEN type = "salida" THEN quantity ELSE 0 END) as total_out')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($data);
    }


    // 5️⃣ Reporte: Producto más vendido (más salidas) en un rango de fechas
    public function topProductByRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $top = InventoryMovement::where('type', 'salida')
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->first();

        return response()->json($top);
    }


    // 6️⃣ Reporte: Consumo promedio semanal y mensual
    public function averageConsumption()
    {
        // Promedio semanal
        $weekly = InventoryMovement::where('type', 'salida')
            ->select(
                DB::raw('strftime("%Y-%W", created_at) as week'),
                DB::raw('SUM(quantity) as total_out')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        // Promedio mensual
        $monthly = InventoryMovement::where('type', 'salida')
            ->select(
                DB::raw('strftime("%Y-%m", created_at) as month'),
                DB::raw('SUM(quantity) as total_out')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'weekly' => [
                'data' => $weekly,
                'average' => $weekly->avg('total_out'),
            ],
            'monthly' => [
                'data' => $monthly,
                'average' => $monthly->avg('total_out'),
            ]
        ]);
    }
}
