<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Migracion extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'attachment',
        // Agrega otros campos que desees asignar masivamente
    ];
}
