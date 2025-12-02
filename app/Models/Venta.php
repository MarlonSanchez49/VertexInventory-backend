<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'mesa_id',
        'metodo_pago_id',
        'total',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'producto_venta')->withPivot('quantity', 'price');
    }
}
