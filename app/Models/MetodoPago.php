<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPago extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}
