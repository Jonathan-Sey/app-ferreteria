<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class MovimientoInventario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimientos_inventario';

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    protected $fillable = [
        'attachment',
        'fecha',
        'id_sucursal',
        'tipo_movimiento',
        'numero_documento',
        'observaciones',
        'sucursal_destino',
    ];

    protected static function boot()
    {
        parent::boot();

        //MIENTRAS
        static::creating(function ($model) {
            $model->created_by = auth()->check() ? auth()->id() : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->check() ? auth()->id() : null;
        });

        static::deleting(function ($model) {
            $model->timestamps = false;
            $model->estado = self::STATUS_DELETED;
            $model->deleted_by = auth()->check() ? auth()->id() : null;
            $model->save();
            $model->timestamps = true;
        });

        //DESPUES DE
        // static::saved(function (MovimientoInventario $movimientoInventario) {
        //     $movimientoInventario->load('detalles'); // Cargar los detalles antes de actualizar el inventario
        //     self::updateInventarioStock($movimientoInventario);
        // });

        // static::updated(function (MovimientoInventario $movimientoInventario) {
        //     self::updateInventarioStock($movimientoInventario);
        // });
    }

    public static function updateInventarioStock(MovimientoInventario $movimientoInventario): void
    {
        Log::info('Actualizando inventario para el movimiento', ['movimiento' => $movimientoInventario]);

        Log::info('Detalles movimiento', ['movimientoDe' => $movimientoInventario->detalles]);

        foreach ($movimientoInventario->detalles as $detalle) {
            $inventarioStock = InventarioStock::firstOrNew([
                'id_producto' => $detalle->id_producto,
                'id_sucursal' => $movimientoInventario->id_sucursal,
            ]);
            Log::info('Entrada de inventario', ['producto_detalle' => $detalle]);
            if ($movimientoInventario->tipo_movimiento === 'ENTRADA') {
                // $inventarioStock->cantidad_actual += $detalle->cantidad;
                $inventarioStock->cantidad_actual = $detalle->cantidad;
            } elseif ($movimientoInventario->tipo_movimiento === 'SALIDA') {
                $inventarioStock->cantidad_actual -= $detalle->cantidad;
            } elseif ($movimientoInventario->tipo_movimiento === 'AJUSTE') {
                $inventarioStock->cantidad_actual += $detalle->cantidad;
            } elseif ($movimientoInventario->tipo_movimiento === 'TRASLADO') {
                $inventarioStock->cantidad_actual -= $detalle->cantidad;

                // Actualizar inventario en la sucursal destino
                $inventarioStockDestino = InventarioStock::firstOrNew([
                    'id_producto' => $detalle->id_producto,
                    'id_sucursal' => $movimientoInventario->sucursal_destino,
                ]);
                $inventarioStockDestino->cantidad_actual += $detalle->cantidad;
                $inventarioStockDestino->save();
            }

            $inventarioStock->save();
        }
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_destino');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(MovimientoInventarioDetalle::class, 'id_movimiento');
    }
}
