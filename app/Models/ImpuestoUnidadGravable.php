<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImpuestoUnidadGravable extends Model
{
    use HasFactory;

    protected $table = 'impuestos_unidad_gravable';
    // Agrega id_cvimpuestostipo a la propiedad fillable
    // protected $fillable = [
    //     'id_cvimpuestostipo',
    //     'nombre_corto',
    //     'tasa_monto',
    //     // Agrega otros campos si es necesario
    // ];

    public function impuestoTipo()
    {
        return $this->belongsTo(ImpuestoTipo::class, 'id_cvimpuestostipo');
    }

    public function ventaItemImpuestos(): HasMany
    {
        return $this->hasMany(VentaItemImpuesto::class, 'id_impuesto');
    }

    /**
     * Los productos que tienen este impuesto
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'producto_impuesto','id_producto','id_impuesto');
    }

    // public function usuarios()
    // {
    //     return $this->belongsToMany(User::class, 'sucursales_usuario', 'id_sucursal', 'id_usuario');
    // }
}
