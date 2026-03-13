<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;
    public function pais()
    {
        return $this->belongsTo(Pais::class,'pais_id');
    }
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }
}
