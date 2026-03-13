<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpuestoTipo extends Model
{
    use HasFactory;

    protected $table = 'impuestos_tipo';

    public function impuestosUnidadGravable()
    {
        return $this->hasMany(ImpuestoUnidadGravable::class, 'id_cvimpuestostipo');
    }
}
