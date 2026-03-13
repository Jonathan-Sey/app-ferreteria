<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'compra_items';

    protected $fillable = [
        'attachment',
        'id_compra',
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

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'precio_parcial' => 'decimal:2',
        'descuento' => 'decimal:2',
        'otros_descuentos' => 'decimal:2',
        'total' => 'decimal:2',
        'impuesto' => 'decimal:2',
    ];

    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class,'id_compra');
    }

    public function productos(): BelongsTo
    {
        return $this->belongsTo(Producto::class,'producto_id');
    }


    // Evento que se ejecuta después de crear un ítem de compra
    protected static function booted()
    {
        static::created(function ($item) {
            $producto = $item->productos;
            
            if ($producto && $producto->tipo !== 'servicio') {
                $sucursalId = $item->purchaseInvoice->id_sucursal;
    
                $inventarioStock = InventarioStock::firstOrNew([
                    'id_producto' => $item->producto_id,
                    'id_sucursal' => $sucursalId,
                ]);
    
                $inventarioStock->cantidad_actual += $item->cantidad;
                $inventarioStock->save();
            }
        });
    }
    // Mutador para cantidad
public function setCantidadAttribute($value)
{
    $this->attributes['cantidad'] = $value ?? 1;
}
} 