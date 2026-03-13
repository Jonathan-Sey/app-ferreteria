<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificadores';

    protected $fillable = [
        'nit',
        'nombre',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
