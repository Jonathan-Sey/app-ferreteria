<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuotaCredito extends Model
{
    protected $table = 'cuotas_credito';

    public function credito()
    {
        return $this->belongsTo(Credito::class);
    }
    use HasFactory;
}
