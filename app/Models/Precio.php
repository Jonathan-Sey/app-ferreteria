<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Precio extends Model
{
    use HasFactory;

    protected $table = "precios";

    protected $fillable = [
        'id_producto',
        'nombre',
        'precio',
        'descripcion'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
