<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MovimientoInventarioDetalle extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario_detalle';

    protected $fillable = [
        'attachment',
        'id_movimiento',
        'id_producto',
        'cantidad',
        'costo_unitario',
    ];

    protected static function boot()
    {
        parent::boot();

        //DESPUES DE
        static::saved(function (MovimientoInventarioDetalle $movimientoInventario) {
            // $movimientoInventario->load('detalles'); // Cargar los detalles antes de actualizar el inventario
            self::updateInventarioStock($movimientoInventario);
        });

        // static::updated(function (MovimientoInventario $movimientoInventario) {
        //     self::updateInventarioStock($movimientoInventario);
        // });
    }

    public static function updateInventarioStock(MovimientoInventarioDetalle $movimientoInventarioDetalle): void
    {
        Log::info('Actualizando inventario2 para el movimiento', ['movimiento' => $movimientoInventarioDetalle]);

        // Log::info('Detalles movimiento', ['movimientoDe' => $movimientoInventario->detalles]);
        // foreach ($movimientoInventario as $detalle) {
            $inventarioStock = InventarioStock::firstOrNew([
                'id_producto' => $movimientoInventarioDetalle->id_producto,
                'id_sucursal' => $movimientoInventarioDetalle->movimientoInventario->id_sucursal,
            ]);
            Log::info('Entrada de inventario', ['producto_detalle' => $movimientoInventarioDetalle]);
            if ($movimientoInventarioDetalle->movimientoInventario->tipo_movimiento === 'ENTRADA') {
                // $inventarioStock->cantidad_actual += $detalle->cantidad;
                $inventarioStock->cantidad_actual = $movimientoInventarioDetalle->cantidad;
            } elseif ($movimientoInventarioDetalle->movimientoInventario->tipo_movimiento === 'SALIDA') {
                $inventarioStock->cantidad_actual -= $movimientoInventarioDetalle->cantidad;
            } elseif ($movimientoInventarioDetalle->movimientoInventario->tipo_movimiento === 'AJUSTE') {
                $inventarioStock->cantidad_actual += $movimientoInventarioDetalle->cantidad;
            } elseif ($movimientoInventarioDetalle->movimientoInventario->tipo_movimiento === 'TRASLADO') {
                $inventarioStock->cantidad_actual -= $movimientoInventarioDetalle->cantidad;

                // Actualizar inventario en la sucursal destino
                $inventarioStockDestino = InventarioStock::firstOrNew([
                    'id_producto' => $movimientoInventarioDetalle->id_producto,
                    'id_sucursal' => $movimientoInventarioDetalle->movimientoInventario->sucursal_destino,
                ]);
                $inventarioStockDestino->cantidad_actual += $movimientoInventarioDetalle->cantidad;
                $inventarioStockDestino->save();
            }

            $inventarioStock->save();
        // }
    }

    public function movimientoInventario()
    {
        return $this->belongsTo(MovimientoInventario::class, 'id_movimiento');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
