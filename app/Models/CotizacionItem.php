<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class CotizacionItem extends Model
{
    use HasFactory; //SoftDeletes;

    protected $table = 'cotizacion_items';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_cotizacion',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'descuento',
        'total',
        'precio_parcial',
    ];

    // Relación con la cotización
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion');
    }

    // Relación con el producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}