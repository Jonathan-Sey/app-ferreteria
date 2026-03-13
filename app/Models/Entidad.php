<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidad extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    protected $table = 'entidades';

    protected $fillable = [
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
        'es_cliente',
        'es_proveedor',
        'es_empresa',
        'estado',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->check() ? auth()->id() : null;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->check() ? auth()->id() : null;
        });

        static::deleting(function ($model) {
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

    public function facturasCompraComoEmisor()
    {
        return $this->hasMany(PurchaseInvoice::class, 'emisor_id');
    }

    public function facturasCompraComoReceptor()
    {
        return $this->hasMany(PurchaseInvoice::class, 'receptor_id');
    }

    public function facturasVentaComoEmisor()
    {
        return $this->hasMany(Venta::class, 'emisor_id');
    }

    public function facturasVentaComoReceptor()
    {
        return $this->hasMany(Venta::class, 'receptor_id');
    }
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_cliente');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('estado', self::STATUS_ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('estado', self::STATUS_DELETED);
    }

    public function scopeClientes($query)
    {
        return $query->where('es_cliente', true);
    }

    public function scopeProveedores($query)
    {
        return $query->where('es_proveedor', true);
    }

    public function scopeEmpresas($query)
    {
        return $query->where('es_empresa', true);
    }
}
