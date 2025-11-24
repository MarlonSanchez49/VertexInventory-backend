<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'type',
        'note'
    ];

    // Relación con Producto
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relación con Bodega
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
