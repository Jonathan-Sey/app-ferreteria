<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "productos";

    protected $fillable = [
        'attachment',
        "codigo",
        "nombre",
        "descripcion",
        "fecha",
        "imagen",
        "id_marca",
        "id_categorias",
        "estado",
        "precio_compra",
        "precio_venta",
        "precio_mayoreo",
        "tipo"
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (request()->has('generar_correlativo') && empty($model->codigo)) {
                $ultimo = self::withTrashed()->orderBy('id', 'desc')->first();
                $correlativo = $ultimo ? str_pad($ultimo->id + 1, 6, '0', STR_PAD_LEFT) : '000001';
                $model->codigo = $correlativo;
            }
        });
    }
    public function getImageUrlAttribute()
    {
        return $this->imagen ? Storage::disk('images_external')->url($this->imagen) : null;
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    // Relación con la tabla categorias
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categorias');
    }

    // Relación con la tabla inventario_stock
    public function inventarioStocks()
    {
        return $this->hasMany(InventarioStock::class, 'id_producto');
    }

    public function cotizacionItems()
    {
        return $this->hasMany(CotizacionItem::class, 'id_producto');
    }

    public function impuestos()
    {
        return $this->belongsToMany(ImpuestoUnidadGravable::class, 'producto_impuesto', 'id_producto', 'id_impuesto');
    }

    public function precios()
    {
        return $this->hasMany(Precio::class, 'id_producto');
    }
//crear relacion con la tabla de compra_items
    // public function compraItems()
    // {
    //     return $this->hasMany(CompraItem::class, 'producto_id', 'id');
    // }
    // Relación con la tabla de impuestos
    
    // Debería ser:
    // public function impuestos()
    // {
    //     return $this->belongsToMany(
    //         ImpuestoUnidadGravable::class, 
    //         'producto_impuesto', 
    //         'id_producto', 
    //         'id_impuesto'
    //     )->withTimestamps();
    // }

}

 