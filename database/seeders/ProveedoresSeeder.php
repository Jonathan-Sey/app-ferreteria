<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            // [
            //     'codigo_interno' => 'P001',
            //     'id_afiliacion_iva' => 1,
            //     'cod_establecimiento' => 'E001',
            //     'correo' => 'proveedor1@example.com',
            //     'nit' => '1234567-8',
            //     'telefono' => '555-1234',
            //     'nombre_comercial' => 'Proveedor 1 S.A.',
            //     'nombre' => 'Proveedor Uno',
            //     'direccion' => 'Calle Falsa 123',
            //     'codigo_postal' => '01001',
            //     'id_municipio' => 1,
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'codigo_interno' => 'P002',
            //     'id_afiliacion_iva' => 2,
            //     'cod_establecimiento' => 'E002',
            //     'correo' => 'proveedor2@example.com',
            //     'nit' => '2345678-9',
            //     'telefono' => '555-5678',
            //     'nombre_comercial' => 'Proveedor 2 S.A.',
            //     'nombre' => 'Proveedor Dos',
            //     'direccion' => 'Avenida Siempre Viva 742',
            //     'codigo_postal' => '01002',
            //     'id_municipio' => 2,
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        Proveedor::insert($proveedores);
    }
}
