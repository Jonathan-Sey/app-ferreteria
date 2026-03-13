<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Cotizacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cotizacions';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_cliente',
        'consumidor_final', // Nuevo campo añadido
        'id_sucursal',
        'fecha_emision',
        'total',
        'estado',
        'notes',
    ];

    // Campos calculados (opcional, si los necesitas)
    protected $appends = ['total', 'subtotal'];

    // Relación con el cliente (Entidad)
    public function cliente()
    {
        return $this->belongsTo(Entidad::class, 'id_cliente')->withDefault([
            'nombre' => $this->consumidor_final ?? 'Consumidor Final'
        ]);
    }

    // Relación con la sucursal
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal');
    }

    // Relación con los ítems de la cotización
    public function items()
    {
        return $this->hasMany(CotizacionItem::class, 'id_cotizacion');
    }

    // Método accesorio para obtener el total de la cotización
    protected function total(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->items()->sum('total');
            }
        );
    }

    // Método accesorio para obtener el subtotal de la cotización
    protected function subtotal(): Attribute
    {
         return Attribute::make(
             get: function () {
                 return $this->items()->sum('precio_parcial');
             }
         );
     }

    // Evento que se ejecuta al eliminar una cotización
    protected static function booted()
    {
        static::saving(function ($cotizacion) {
            if (empty($cotizacion->id_cliente) && empty($cotizacion->consumidor_final)) {
                throw new \Exception('Debe especificar un cliente o un consumidor final');
            }

            if (!empty($cotizacion->consumidor_final) && strtoupper(trim($cotizacion->consumidor_final)) === 'CF') {
                $cotizacion->consumidor_final = 'Consumidor Final';
            }

            if (!empty($cotizacion->consumidor_final)) {
                $cotizacion->id_cliente = null;
            }
        });

        static::deleting(function ($cotizacion) {
            // Eliminar todos los ítems relacionados
            $cotizacion->items()->delete();
        });
    }
}