<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventarioStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventario_stock';

    protected $fillable = [
        'attachment',
        'id_producto',
        'id_sucursal',
        'cantidad_actual',
        'stock_minimo',
        'ubicacion',
    ];

    // public static function updateStock($id_producto, $id_sucursal, $cantidad, $tipo_movimiento, $sucursal_destino = null)
    // {
    //     $inventarioStock = self::firstOrNew([
    //         'id_producto' => $id_producto,
    //         'id_sucursal' => $id_sucursal,
    //     ]);

    //     if ($tipo_movimiento === 'ENTRADA') {
    //         $inventarioStock->cantidad_actual += $cantidad;
    //     } elseif ($tipo_movimiento === 'SALIDA') {
    //         $inventarioStock->cantidad_actual -= $cantidad;
    //     } elseif ($tipo_movimiento === 'AJUSTE') {
    //         $inventarioStock->cantidad_actual = $cantidad;
    //     } elseif ($tipo_movimiento === 'TRASLADO') {
    //         $inventarioStock->cantidad_actual -= $cantidad;

    //         // Actualizar inventario en la sucursal destino
    //         $inventarioStockDestino = self::firstOrNew([
    //             'id_producto' => $id_producto,
    //             'id_sucursal' => $sucursal_destino,
    //         ]);
    //         $inventarioStockDestino->cantidad_actual += $cantidad;
    //         $inventarioStockDestino->save();
    //     }

    //     $inventarioStock->save();
    // }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }
}