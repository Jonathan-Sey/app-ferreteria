<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;



class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $table = "compras";

    protected $fillable = [
        'attachment',
        'id_moneda',
        'tipoComprobante',
        'id_tipoDte',
        'id_proveedor',
        'fechahora_emision',
        'id_certificador',
        'no_autorizacion',
        'serie',
        'codigo_autorizacion',
        'fechahora_certificacion',
        'notes',
        'estado',
        'id_sucursal',
    ];

    protected $casts = [
        'fechahora_emision' => 'date',
    ];

    public function monedas(): BelongsTo
    {
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }
    // public function proveedores(): BelongsTo
    // {
    //     return $this->belongsTo(Proveedor::class, 'id_proveedor');
    // }
    // public function emisor()
    // {
    //     return $this->belongsTo(Entidad::class, 'emisor_id');
    // }
    public function proveedores()
    {
        return $this->belongsTo(Entidad::class, 'id_proveedor');
    }

    public function receptor()
    {
        return $this->belongsTo(Entidad::class, 'id_receptor');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class, 'id_compra');
    }

    // Relación con Sucursal
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }


    protected function total(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->items()
                    ->sum('total');
            }
        );
    }

    // Evento que se ejecuta antes de eliminar una compra
    protected static function booted()
    {
        static::deleting(function ($invoice) {

                // Revertir el inventario para cada ítem
            foreach ($invoice->items as $item) {
                $inventarioStock = InventarioStock::where('id_producto', $item->producto_id)
                                                ->where('id_sucursal', $invoice->id_sucursal)
                                                ->first();

                if ($inventarioStock) {
                    $inventarioStock->cantidad_actual -= $item->cantidad;
                    $inventarioStock->save();
                }
            }
            // Eliminar todos los ítems relacionados
            $invoice->items()->delete();
        });
    }
}
