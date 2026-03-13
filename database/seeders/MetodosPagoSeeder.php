<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MetodosPagoSeeder extends Seeder
{
    private const METODOS_BASE = [
        [
            'nombre' => 'Efectivo',
            'descripcion' => 'Pago en moneda local (quetzales)',
            'activo' => true
        ],
        [
            'nombre' => 'Tarjeta', 
            'descripcion' => 'Pago con tarjeta de crédito/débito',
            'activo' => true
        ],
        [
            'nombre' => 'Transferencia',
            'descripcion' => 'Pago por transferencia bancaria',
            'activo' => true
        ]
    ];

    public function run(): void
    {
        try {
            if (DB::table('metodos_pago')->exists() && 
                DB::table('metodos_pago')->count() > 0) {
                Log::info('Seeder de métodos de pago omitido: ya existen registros');
                return;
            }

            DB::transaction(function () {
                foreach (self::METODOS_BASE as $metodo) {
                    DB::table('metodos_pago')->insert([
                        ...$metodo,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            });

            Log::info('Métodos de pago base creados exitosamente');
            
        } catch (\Exception $e) {
            Log::error('Error en MetodosPagoSeeder: '.$e->getMessage());
            throw $e;
        }
    }
}