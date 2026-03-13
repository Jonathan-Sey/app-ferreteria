<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteDinamico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo_documento',
        'aflas',
        'cantidad_codedores',
        'testigo',
        'creditos_inmediatos',
        'ahorro_programado',
        'attachment',
        // Agrega los campos para el tipo de reporte:
        'tipo',
        'descripcion',
    ];
}