<?php

namespace Database\Seeders;

use App\Models\Documento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Documento::insert([
            ['tipo_documento' => 'DPI', 'descripcion' => 'Documento Personal de Identificacion', 'estado' => 1],
            ['tipo_documento' => 'Pasaporte', 'descripcion' => 'Pasaporte personal', 'estado' => 1],
            ['tipo_documento' => 'NIT', 'descripcion' => 'Numero de identificacion tributaria', 'estado' => 1],
        ]);
    }
}
