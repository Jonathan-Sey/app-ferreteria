<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VentaItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas_item';

    protected $fillable = [
        'attachment',
        'id_venta',
        'numerolinea',
        'cantidad',
        'producto_id',
        'precio_unitario',
        'precio_parcial',
        'descuento',
        'otros_descuentos',
        'total',
        'impuesto',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function impuestos(): HasMany
    {
        return $this->hasMany(VentaItemImpuesto::class, 'id_venta_item');
    }



    // Evento que se ejecuta después de crear un ítem de compra
    protected static function booted()
    {
        static::created(function ($item) {
            // Obtener la sucursal de la compra
            $sucursalId = $item->venta->id_sucursal;

            // Actualizar el inventario
            $inventarioStock = InventarioStock::firstOrNew([
                'id_producto' => $item->producto_id,
                'id_sucursal' => $sucursalId,
            ]);

            $inventarioStock->cantidad_actual -= $item->cantidad;
            $inventarioStock->save();
        });
    }
}