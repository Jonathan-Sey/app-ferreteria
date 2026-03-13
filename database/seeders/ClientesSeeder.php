<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Entidad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'tipo_entidad' => 1,
                'codigo_interno' => 'C001',
                'id_afiliacion_iva' => 1,
                'cod_establecimiento' => '1',
                'correo' => 'cliente1@example.com',
                'nit' => 'CF',
                'telefono' => '555-1234',
                'nombre_comercial' => 'Cliente 1 S.A.',
                'nombre' => 'Cliente Uno',
                'direccion' => 'Calle Falsa 123',
                'codigo_postal' => '01001',
                'id_municipio' => 1,
                'es_cliente' => 1,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'tipo_entidad' => 2,
            //     'codigo_interno' => 'C002',
            //     'id_afiliacion_iva' => 2,
            //     'cod_establecimiento' => 'E002',
            //     'correo' => 'cliente2@example.com',
            //     'nit' => '2345678-9',
            //     'telefono' => '555-5678',
            //     'nombre_comercial' => 'Cliente 2 S.A.',
            //     'nombre' => 'Cliente Dos',
            //     'direccion' => 'Avenida Siempre Viva 742',
            //     'codigo_postal' => '01002',
            //     'id_municipio' => 2,
            //     'es_cliente' => 1,
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        Entidad::insert($clientes);
    }
}
