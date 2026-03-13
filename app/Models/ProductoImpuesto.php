<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProductoImpuesto extends Model
{

    protected $table = "producto_impuesto";
    protected $fillable = ["id_impuesto", "id_producto"];


    // protected static function boot(){
    //     parent::boot();

    //     static::creating(function ($model) {
    //         Log::info($model);
    //     });

    // }

    use HasFactory;

    public function producto()
    {
        return $this->belongsTo(Producto::class,"id_producto");
    }

    public function impuesto(){
        return $this->belongsTo(ImpuestoUnidadGravable::class,"id_impuesto");
    }
}
