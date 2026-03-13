<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;
    public function departamento()
    {
        return $this->belongsTo(Departamento::class,'departamento_id');
    }
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_municipio');
    }
    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'id_municipio');
    }
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_municipio');
    }
}
