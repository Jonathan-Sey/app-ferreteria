<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 0;

    protected $table = 'sucursales';
    protected $fillable = [
        'attachment',
        'nombre',
        'direccion',
        'telefono',
        'id_municipio',
        'tipo',
        'estado',
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
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'id_municipio');
    }
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'sucursales_usuario', 'id_sucursal', 'id_usuario');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'id_sucursal');
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
