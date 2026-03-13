<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class VentaMetodoPagoPivot extends Pivot
{
    protected $table = 'venta_metodo_pago';
    
    protected $casts = [
        'monto' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    protected $fillable = [
        'metodo_pago_id',
        'venta_id',
        'monto',
        'referencia'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class,"venta_id");
    }

    public function metodoPago(){
        return $this->belongsTo(MetodoPago::class,"metodo_pago_id");
    }

}