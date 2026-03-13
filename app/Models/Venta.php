<?php

namespace App\Models;

use App\Enums\FacturasStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';
    protected $appends = ['total', 'subtotal', 'total_impuestos'];

    protected $fillable = [
        'attachment',
        'tipoComprobante',
        'id_moneda',
        'id_tipoDte',
        'id_emisor',
        'id_cliente',
        'consumidor_final',
        'certificada',
        'fechahora_emision',
        'id_certificador',
        'no_autorizacion',
        'serie',
        'codigo_autorizacion',
        'fechahora_certificacion',
        'notes',
        'estado',
        'id_sucursal',
        'created_by',
    ];

    protected $casts = [
        'estado' => FacturasStatus::class,
        'fechahora_emision' => 'datetime',
        'fechahora_certificacion' => 'datetime',
    ];

    protected static function booted()
    {

        static::creating(function ($venta) {
            if (empty($venta->created_by)) {
                $venta->created_by = auth()->id();
            }
        });

        static::saving(function ($venta) {
           
            if (empty($venta->id_cliente) && empty($venta->consumidor_final)) {
                throw new \Exception('Debe especificar un cliente o un consumidor final');
            }

            if (!empty($venta->consumidor_final) && strtoupper(trim($venta->consumidor_final)) === 'CF') {
                $venta->consumidor_final = 'Consumidor Final';
            }

            if (!empty($venta->consumidor_final)) {
                $venta->id_cliente = null;
            }

           
        });

        static::deleting(function ($venta) {
            foreach ($venta->items as $item) {
                $inventarioStock = InventarioStock::where('id_producto', $item->producto_id)
                    ->where('id_sucursal', $venta->id_sucursal)
                    ->first();

                if ($inventarioStock) {
                    $inventarioStock->cantidad_actual += $item->cantidad;
                    $inventarioStock->save();
                }
            }
            $venta->items()->delete();
           
        });
    }

    protected function total(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->items()->sum('total')
        );
    }

    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->items()->sum('precio_parcial')
        );
    }

    protected function totalImpuestos(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->items()->sum('impuesto')
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(VentaItem::class, 'id_venta');
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class, 'id_moneda');
    }

    public function tipoDte(): BelongsTo
    {
        return $this->belongsTo(TipoDte::class, 'id_tipoDte');
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'id_emisor');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Entidad::class, 'id_cliente')->withDefault([
            'nombre' => $this->consumidor_final ?? 'Consumidor Final'
        ]);
    }

    public function certificador(): BelongsTo
    {
        return $this->belongsTo(Certificador::class, 'id_certificador');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


   
    public function paymentMethods()
    {
        return $this->hasMany(VentaMetodoPagoPivot::class, 'venta_id');
    }


 
}
