<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbCTipdoc extends Model
{
    use HasFactory;

    // Define el nombre de la tabla si no sigue la convención plural
    protected $table = 'tb_ctipdoc';

    // Define los campos asignables en masa
    protected $fillable = [
        'tipdoc',
        'desc',
    ];
}