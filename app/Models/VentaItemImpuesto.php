<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaItemImpuesto extends Model
{
    use HasFactory;
    protected $table = 'venta_item_impuestos';

    protected $fillable = [
        'id_venta_item',
        'id_impuesto',
        'monto_gravable',
        'monto_impuesto'
    ];

    public function ventaItem()
    {
        return $this->belongsTo(VentaItem::class, 'id_venta_item');
    }

    public function impuesto()
    {
        return $this->belongsTo(ImpuestoUnidadGravable::class, 'id_impuesto');
    }
}
