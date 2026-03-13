<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    protected $table = 'creditos';
    protected $fillable = [
        'venta_id',
        'monto_total',
        'saldo_pendiente',
        'plazo',
        'tasa_interes',
        'estado',
        'fecha_inicio'
    ];
    
    protected $casts = [
        'monto_total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'tasa_interes' => 'decimal:2',
        'fecha_inicio' => 'date'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function cuotas()
    {
        return $this->hasMany(CuotaCredito::class);
    }
    use HasFactory;
}
