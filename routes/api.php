<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Controladores
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\InventoryMovementController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\MesaController;
use App\Http\Controllers\Api\MetodoPagoController;

// Rutas Públicas (No requieren Token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas (Requieren Token de Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->load('role'); // Cargar la relación 'role'
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id'   => $user->role_id,
            'role_name' => $user->role ? $user->role->name : null,
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    // Aquí van tus rutas protegidas

    //Crud Productos
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    //Crud Categorías
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    //Crud Proveedores
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);

    // CRUD Empleados
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

    //CRUD  Roles
    Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);


    // CRUD Warehouses (Bodegas)
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::post('/warehouses', [WarehouseController::class, 'store']);
    Route::get('/warehouses/{id}', [WarehouseController::class, 'show']);
    Route::put('/warehouses/{id}', [WarehouseController::class, 'update']);
    Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy']);

    // CRUD Inventario
    Route::get('/inventories', [InventoryController::class, 'index']);
    Route::post('/inventories', [InventoryController::class, 'store']);
    Route::get('/inventories/{id}', [InventoryController::class, 'show']);
    Route::put('/inventories/{id}', [InventoryController::class, 'update']);
    Route::delete('/inventories/{id}', [InventoryController::class, 'destroy']);

    //Crud de Movimientos de Inventario
    Route::get('/movements', [InventoryMovementController::class, 'index']);
    Route::post('/movements', [InventoryMovementController::class, 'store']);
    Route::get('/movements/{id}', [InventoryMovementController::class, 'show']);
    Route::put('/movements/{id}', [InventoryMovementController::class, 'update']);
    Route::delete('/movements/{id}', [InventoryMovementController::class, 'destroy']);

    //Apartado de reportes
    Route::get('/reports/inventory-value', [ReportController::class, 'inventoryValue']); //Valor total del inventario (precio × stock por producto)
    Route::get('/reports/top-products', [ReportController::class, 'topProducts']); //Top 5 productos más movidos (entradas + salidas)
    Route::get('/reports/low-stock', [ReportController::class, 'lowStock']); //Productos con bajo stock (por defecto < 10 unidades)
    Route::get('/reports/movements-by-month', [ReportController::class, 'movementsByMonth']); //Entradas vs salidas agrupadas por mes (para gráficas)
    Route::get('/reports/top-product-range', [ReportController::class, 'topProductByRange']); //Producto más vendido en un rango de fechas  parametro: start_date=YYYY-MM-DD, end_date=YYYY-MM-DD
    Route::get('/reports/average-consumption', [ReportController::class, 'averageConsumption']); //Consumo promedio semanal y mensual (solo salidas)

    // CRUD Ventas
    Route::apiResource('ventas', VentaController::class);
    Route::apiResource('mesas', MesaController::class);
    Route::apiResource('metodos-pago', MetodoPagoController::class);
});
