<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    protected $table = 'proveedores';
    protected $fillable = [
        'attachment',
        'tipo_entidad',
        'codigo_interno',
        'id_afiliacion_iva',
        'cod_establecimiento',
        'correo',
        'nit',
        'telefono',
        'nombre_comercial',
        'nombre',
        'direccion',
        'codigo_postal',
        'id_municipio',
        'estado',
        'created_by',
        'updated_by',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // $model->created_by = auth()->id();
            $model->created_by = auth()->check() ? auth()->id() : null;
        });

        static::updating(function ($model) {
            // $model->updated_by = auth()->id();
            $model->updated_by = auth()->check() ? auth()->id() : null;
        });

        static::deleting(function ($model) {
            // $model->deleted_by = auth()->id();
            $model->timestamps = false;
            $model->estado = self::STATUS_DELETED;
            $model->deleted_by = auth()->check() ? auth()->id() : null;
            $model->save();
            $model->timestamps = true;
        });
    }
    // Relaciones 
    public function afiliacionIva()
    {
        return $this->belongsTo(AfiliacionIva::class, 'id_afiliacion_iva');
    }
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }

    //crear relacion con facturas de compra
    public function facturasCompra()
    {
        return $this->hasMany(PurchaseInvoice::class, 'id_proveedor');
    }

    public function scopeActive($query)
    {
        return $query->where('estado', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('estado', self::STATUS_DELETED);
    }
}
