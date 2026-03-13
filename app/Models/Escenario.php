<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escenario extends Model
{
    use HasFactory;

    protected $table = 'escenarios';

    public function frase()
    {
        return $this->belongsTo(Frase::class, 'id_frases');
    }
}
