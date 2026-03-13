<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MetodoPago extends Model
{
    use HasFactory;

    protected $table = 'metodos_pago';
    
    protected $fillable = ['activo', 'descripcion'];
    
    protected $guarded = ['id', 'nombre'];
    
    protected $attributes = [
        'activo' => true,
        'descripcion' => ''
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        
    }

    public function ventas(): BelongsToMany
    {
        return $this->belongsToMany(Venta::class, 'venta_metodo_pago','venta_id','metodo_pago_id')
                   ->withPivot(['monto', 'referencia'])
                   ->withTimestamps();
    }

    protected static function booted()
    {
        static::deleting(function ($metodo) {
            if ($metodo->ventas()->exists()) {
                throw new \RuntimeException(
                    'No se puede eliminar un método de pago con ventas asociadas'
                );
            }
        });
    }

    
}