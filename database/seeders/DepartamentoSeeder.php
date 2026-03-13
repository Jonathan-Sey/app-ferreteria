<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Departamento::insert([
            // Guatemala 
            ['id' => 1, 'codigo' => '010', 'nombre' => 'Guatemala', 'pais_id' => 1],
            ['id' => 2, 'codigo' => '020', 'nombre' => 'El progreso', 'pais_id' => 1],
            ['id' => 3, 'codigo' => '030', 'nombre' => 'Sacatepéquez', 'pais_id' => 1],
            ['id' => 4, 'codigo' => '040', 'nombre' => 'Chimaltenango', 'pais_id' => 1],
            ['id' => 5, 'codigo' => '050', 'nombre' => 'Escuintla', 'pais_id' => 1],
            ['id' => 6, 'codigo' => '060', 'nombre' => 'Santa Rosa', 'pais_id' => 1],
            ['id' => 7, 'codigo' => '070', 'nombre' => 'Sololá', 'pais_id' => 1],
            ['id' => 8, 'codigo' => '080', 'nombre' => 'Totonicapán', 'pais_id' => 1],
            ['id' => 9, 'codigo' => '090', 'nombre' => 'Quetzaltenango', 'pais_id' => 1],
            ['id' => 10, 'codigo' => '100', 'nombre' => 'Suchitepequez', 'pais_id' => 1],
            ['id' => 11, 'codigo' => '110', 'nombre' => 'Retalhuleu', 'pais_id' => 1],
            ['id' => 12, 'codigo' => '120', 'nombre' => 'San Marcos', 'pais_id' => 1],
            ['id' => 13, 'codigo' => '130', 'nombre' => 'Huehuetenango', 'pais_id' => 1],
            ['id' => 14, 'codigo' => '140', 'nombre' => 'El Quiché', 'pais_id' => 1],
            ['id' => 15, 'codigo' => '150', 'nombre' => 'Baja Verapaz', 'pais_id' => 1],
            ['id' => 16, 'codigo' => '160', 'nombre' => 'Alta Verapaz', 'pais_id' => 1],
            ['id' => 17, 'codigo' => '170', 'nombre' => 'Petén', 'pais_id' => 1],
            ['id' => 18, 'codigo' => '180', 'nombre' => 'Izabal', 'pais_id' => 1],
            ['id' => 19, 'codigo' => '190', 'nombre' => 'Zacapa', 'pais_id' => 1],
            ['id' => 20, 'codigo' => '200', 'nombre' => 'Chiquimula', 'pais_id' => 1],
            ['id' => 21, 'codigo' => '210', 'nombre' => 'Jalapa', 'pais_id' => 1],
            ['id' => 22, 'codigo' => '220', 'nombre' => 'Jutiapa', 'pais_id' => 1],
            // Honduras,'codigo'=>'',
            ['id' => 23, 'codigo' => '', 'nombre' => 'Atlántida', 'pais_id' => 12],
            ['id' => 24, 'codigo' => '', 'nombre' => 'Colón', 'pais_id' => 12],
            ['id' => 25, 'codigo' => '', 'nombre' => 'Comayagua', 'pais_id' => 12],
            ['id' => 26, 'codigo' => '', 'nombre' => 'Copán', 'pais_id' => 12],
            ['id' => 27, 'codigo' => '', 'nombre' => 'Cortés', 'pais_id' => 12],
            ['id' => 28, 'codigo' => '', 'nombre' => 'Choluteca', 'pais_id' => 12],
            ['id' => 29, 'codigo' => '', 'nombre' => 'El Paraíso', 'pais_id' => 12],
            ['id' => 30, 'codigo' => '', 'nombre' => 'Francisco Morazán', 'pais_id' => 12],
            ['id' => 31, 'codigo' => '', 'nombre' => 'Gracias a Dios', 'pais_id' => 12],
            ['id' => 32, 'codigo' => '', 'nombre' => 'Intibucá', 'pais_id' => 12],
            ['id' => 33, 'codigo' => '', 'nombre' => 'Islas de la Bahía', 'pais_id' => 12],
            ['id' => 34, 'codigo' => '', 'nombre' => 'La Paz', 'pais_id' => 12],
            ['id' => 35, 'codigo' => '', 'nombre' => 'Lempira', 'pais_id' => 12],
            ['id' => 36, 'codigo' => '', 'nombre' => 'Ocotepeque', 'pais_id' => 12],
            ['id' => 37, 'codigo' => '', 'nombre' => 'Olancho', 'pais_id' => 12],
            ['id' => 38, 'codigo' => '', 'nombre' => 'Santa Bárbara', 'pais_id' => 12],
            ['id' => 39, 'codigo' => '', 'nombre' => 'Valle', 'pais_id' => 12],
            ['id' => 40, 'codigo' => '', 'nombre' => 'Yoro', 'pais_id' => 12],
            // El Salva,'codigo'=>'',or 
            ['id' => 41, 'codigo' => '', 'nombre' => 'Ahuachapán', 'pais_id' => 10],
            ['id' => 42, 'codigo' => '', 'nombre' => 'Cabañas', 'pais_id' => 10],
            ['id' => 43, 'codigo' => '', 'nombre' => 'Chalatenango', 'pais_id' => 10],
            ['id' => 44, 'codigo' => '', 'nombre' => 'Cuscatlán', 'pais_id' => 10],
            ['id' => 45, 'codigo' => '', 'nombre' => 'La Libertad', 'pais_id' => 10],
            ['id' => 46, 'codigo' => '', 'nombre' => 'La Paz', 'pais_id' => 10],
            ['id' => 47, 'codigo' => '', 'nombre' => 'La Unión', 'pais_id' => 10],
            ['id' => 48, 'codigo' => '', 'nombre' => 'Morazán', 'pais_id' => 10],
            ['id' => 49, 'codigo' => '', 'nombre' => 'San Miguel', 'pais_id' => 10],
            ['id' => 50, 'codigo' => '', 'nombre' => 'San Salvador', 'pais_id' => 10],
            ['id' => 51, 'codigo' => '', 'nombre' => 'Santa Ana', 'pais_id' => 10],
            ['id' => 52, 'codigo' => '', 'nombre' => 'San Vicente', 'pais_id' => 10],
            ['id' => 53, 'codigo' => '', 'nombre' => 'Sonsonate', 'pais_id' => 10],
            ['id' => 54, 'codigo' => '', 'nombre' => 'Usulután', 'pais_id' => 10],
            // Nicaragu,'codigo'=>'', 
            ['id' => 55, 'codigo' => '', 'nombre' => 'Boaco', 'pais_id' => 14],
            ['id' => 56, 'codigo' => '', 'nombre' => 'Carazo', 'pais_id' => 14],
            ['id' => 57, 'codigo' => '', 'nombre' => 'Chinandega', 'pais_id' => 14],
            ['id' => 58, 'codigo' => '', 'nombre' => 'Chontales', 'pais_id' => 14],
            ['id' => 59, 'codigo' => '', 'nombre' => 'Estelí', 'pais_id' => 14],
            ['id' => 60, 'codigo' => '', 'nombre' => 'Granada', 'pais_id' => 14],
            ['id' => 61, 'codigo' => '', 'nombre' => 'Jinotega', 'pais_id' => 14],
            ['id' => 62, 'codigo' => '', 'nombre' => 'León', 'pais_id' => 14],
            ['id' => 63, 'codigo' => '', 'nombre' => 'Madriz', 'pais_id' => 14],
            ['id' => 64, 'codigo' => '', 'nombre' => 'Managua', 'pais_id' => 14],
            ['id' => 65, 'codigo' => '', 'nombre' => 'Masaya', 'pais_id' => 14],
            ['id' => 66, 'codigo' => '', 'nombre' => 'Matagalpa', 'pais_id' => 14],
            ['id' => 67, 'codigo' => '', 'nombre' => 'Nueva Segovia', 'pais_id' => 14],
            ['id' => 68, 'codigo' => '', 'nombre' => 'Río San Juan', 'pais_id' => 14],
            ['id' => 69, 'codigo' => '', 'nombre' => 'Rivas', 'pais_id' => 14],
            ['id' => 70, 'codigo' => '', 'nombre' => 'Caribe Norte', 'pais_id' => 14],
            ['id' => 71, 'codigo' => '', 'nombre' => 'Caribe Sur', 'pais_id' => 14],
            // Costa Ri,'codigo'=>'',a 
            ['id' => 72, 'codigo' => '', 'nombre' => 'San José', 'pais_id' => 7],
            ['id' => 73, 'codigo' => '', 'nombre' => 'Alajuela', 'pais_id' => 7],
            ['id' => 74, 'codigo' => '', 'nombre' => 'Cartago', 'pais_id' => 7],
            ['id' => 75, 'codigo' => '', 'nombre' => 'Heredia', 'pais_id' => 7],
            ['id' => 76, 'codigo' => '', 'nombre' => 'Guanacaste', 'pais_id' => 7],
            ['id' => 77, 'codigo' => '', 'nombre' => 'Puntarenas', 'pais_id' => 7],
            ['id' => 78, 'codigo' => '', 'nombre' => 'Limón', 'pais_id' => 7]
        ]);
    }
}
