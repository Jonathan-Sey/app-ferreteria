<?php

namespace Database\Seeders;

use App\Models\ImpuestoUnidadGravable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImpuestosUnidadGravableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $impuestosUnidadGravable = [
            ['id' => 1, 'codigo' => 1, 'nombre' => 'Tasa 12.00%', 'nombre_corto' => 'IVA 12%', 'tasa_monto' => 12.00, 'id_cvimpuestostipo' => 1],
            ['id' => 2, 'codigo' => 2, 'nombre' => 'Tasa 0 (Cero)', 'nombre_corto' => 'IVA 0% ', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 1],
            ['id' => 3, 'codigo' => 1, 'nombre' => 'Gasolina Superior', 'nombre_corto' => 'Gasolina Superior Q4.70', 'tasa_monto' => 4.70, 'id_cvimpuestostipo' => 2],
            ['id' => 4, 'codigo' => 2, 'nombre' => 'Gasolina Regular', 'nombre_corto' => 'Gasolina Regular Q4.60', 'tasa_monto' => 4.60, 'id_cvimpuestostipo' => 2],
            ['id' => 5, 'codigo' => 3, 'nombre' => 'Gasolina de Aviación', 'nombre_corto' => 'Gasolina de Aviasión Q4.70', 'tasa_monto' => 4.70, 'id_cvimpuestostipo' => 2],
            ['id' => 6, 'codigo' => 4, 'nombre' => 'Diésel', 'nombre_corto' => 'Diésel Q1.30', 'tasa_monto' => 1.30, 'id_cvimpuestostipo' => 2],
            ['id' => 7, 'codigo' => 5, 'nombre' => 'Gas Oil', 'nombre_corto' => 'Gas Oil Q1.30', 'tasa_monto' => 1.30, 'id_cvimpuestostipo' => 2],
            ['id' => 8, 'codigo' => 6, 'nombre' => 'Kerosina (DPK), (Avjet, turbo fuel)', 'nombre_corto' => 'Kerosina avjet turbo fuel Q0.50', 'tasa_monto' => 0.50, 'id_cvimpuestostipo' => 2],
            ['id' => 9, 'codigo' => 7, 'nombre' => 'Nafta', 'nombre_corto' => 'Nafta Q0.50', 'tasa_monto' => 0.50, 'id_cvimpuestostipo' => 2],
            ['id' => 10, 'codigo' => 8, 'nombre' => 'Fuel Oil (Bunker C)', 'nombre_corto' => 'Fuel Oil (Bunker C) Q0.00', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 2],
            ['id' => 11, 'codigo' => 9, 'nombre' => 'Gas licuado de petróleo a granel', 'nombre_corto' => 'Gas licuado granel Q0.50', 'tasa_monto' => 0.50, 'id_cvimpuestostipo' => 2],
            ['id' => 12, 'codigo' => 10, 'nombre' => 'Gas licuado petróleo carburación', 'nombre_corto' => 'Gas licuado carburación Q0.50', 'tasa_monto' => 0.50, 'id_cvimpuestostipo' => 2],
            ['id' => 13, 'codigo' => 11, 'nombre' => 'Petróleo crudo usado como combustible', 'nombre_corto' => 'Petróleo crudo combustible Q0.00', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 2],
            ['id' => 14, 'codigo' => 12, 'nombre' => 'Otros combustibles derivados del petróleo', 'nombre_corto' => 'Otros combustibles Q0.00', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 2],
            ['id' => 15, 'codigo' => 13, 'nombre' => 'Asfaltos', 'nombre_corto' => 'Asfaltos Q0.00', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 2],
            ['id' => 16, 'codigo' => 1, 'nombre' => 'Tasa Hospedaje 10%', 'nombre_corto' => 'Hospedaje 10.00%', 'tasa_monto' => 10.00, 'id_cvimpuestostipo' => 3],
            ['id' => 17, 'codigo' => 2, 'nombre' => 'Tasa Hospedaje Exento Tasa 0', 'nombre_corto' => 'Hospedaje Exento 0%', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 3],
            ['id' => 18, 'codigo' => 1, 'nombre' => 'Salida del país por vía aérea', 'nombre_corto' => 'Aérea USD30.00', 'tasa_monto' => 30.00, 'id_cvimpuestostipo' => 4],
            ['id' => 19, 'codigo' => 2, 'nombre' => 'Salida del país por vía marítima', 'nombre_corto' => 'Marítima USD10.00', 'tasa_monto' => 10.00, 'id_cvimpuestostipo' => 4],
            ['id' => 20, 'codigo' => 3, 'nombre' => 'Salida del país por vía aérea exento', 'nombre_corto' => 'Aérea exenta Dec. 31-2022', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 4],
            ['id' => 21, 'codigo' => 1, 'nombre' => 'Timbre de prensa cinco (5) por millar', 'nombre_corto' => 'Timbre de prensa', 'tasa_monto' => 0.50, 'id_cvimpuestostipo' => 5],
            ['id' => 22, 'codigo' => 1, 'nombre' => 'Impuesto por seguro contra incendios', 'nombre_corto' => 'Bomberos 2.00%', 'tasa_monto' => 2.00, 'id_cvimpuestostipo' => 6],
            ['id' => 23, 'codigo' => 0, 'nombre' => 'Texto variable (nombre del departamento, municipio y concepto)', 'nombre_corto' => 'Texto Variable', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 7],
            ['id' => 24, 'codigo' => 1, 'nombre' => 'Cervezas y otras bebidas de cereales fermentados, a que se refiere la fracción arancelaria 2203.00.0', 'nombre_corto' => 'Cervezas y otras bebidas 6.00%', 'tasa_monto' => 6.00, 'id_cvimpuestostipo' => 8],
            ['id' => 25, 'codigo' => 2, 'nombre' => 'Vinos, a que se refiere la partida arancelaria 2204.', 'nombre_corto' => 'Vinos 7.50%', 'tasa_monto' => 7.50, 'id_cvimpuestostipo' => 8],
            ['id' => 26, 'codigo' => 3, 'nombre' => 'Vino espumoso, a que se refiere la fracción arancelaria 2204.10.00.', 'nombre_corto' => 'Vino espumoso 7.50%', 'tasa_monto' => 7.50, 'id_cvimpuestostipo' => 8],
            ['id' => 27, 'codigo' => 4, 'nombre' => 'Vino “vermouth”, a que se refiere la partida arancelaria 2205.', 'nombre_corto' => 'Vino “vermouth” 7.50%', 'tasa_monto' => 7.50, 'id_cvimpuestostipo' => 8],
            ['id' => 28, 'codigo' => 5, 'nombre' => 'Sidras, a que se refiere la fracción arancelaria 2206.00.00.', 'nombre_corto' => 'Sidras 7.50%', 'tasa_monto' => 7.50, 'id_cvimpuestostipo' => 8],
            ['id' => 29, 'codigo' => 6, 'nombre' => 'Bebidas alcohólicas destiladas, a que se refiere la partida arancelaria 2208.', 'nombre_corto' => 'Bebidas alcohólicas destiladas 8.50%', 'tasa_monto' => 8.50, 'id_cvimpuestostipo' => 8],
            ['id' => 30, 'codigo' => 7, 'nombre' => 'Bebidas alcohólicas mezcladas con agua gaseosa, agua simple, jugos naturales o endulzada o de cualquier naturaleza, que contenga o no gas carbónico y que sean envasadas en cualquier tipo de recipiente, a que se refiere la fracción arancelaria 2208.90.90.', 'nombre_corto' => 'Bebidas alcohólicas mezcladas 7.50%', 'tasa_monto' => 7.50, 'id_cvimpuestostipo' => 8],
            ['id' => 31, 'codigo' => 8, 'nombre' => 'Otras bebidas fermentadas, a que se refiere la fracción arancelaria 2206.00.00', 'nombre_corto' => 'Otras bebidas fermentadas 7.50%', 'tasa_monto' => 7.50, 'id_cvimpuestostipo' => 8],
            ['id' => 32, 'codigo' => 1, 'nombre' => 'Impuesto sobre el precio de venta en fábrica por paquete de 10 cajetillas de 20 cigarrillos cada una', 'nombre_corto' => 'Precio de fábrica por 100%', 'tasa_monto' => 100.00, 'id_cvimpuestostipo' => 9],
            ['id' => 33, 'codigo' => 2, 'nombre' => 'Impuesto sobre el precio de venta sugerido al consumidor por paquete de 10 cajetillas de 20 cigarril', 'nombre_corto' => 'Precio sugerido al consumidor por 75%', 'tasa_monto' => 75.00, 'id_cvimpuestostipo' => 9],
            ['id' => 34, 'codigo' => 1, 'nombre' => 'Bolsa de 42.5 kilogramos', 'nombre_corto' => 'Bolsa de 42.5 kg Q1.50', 'tasa_monto' => 1.50, 'id_cvimpuestostipo' => 10],
            ['id' => 35, 'codigo' => 1, 'nombre' => 'Bebidas gaseosas simples o endulzadas que contengan o no, gas carbónico, a que se refieren las partidas arancelarias 2201 y 2202. Así como los jarabes y/o concentrados de cuya mezcla se generen bebidas gaseosas, a que se refiere la fracción arancelaria 2106.90.30.', 'nombre_corto' => 'Bebidas gaseosas y jarabes Q0.18', 'tasa_monto' => 0.18, 'id_cvimpuestostipo' => 11],
            ['id' => 36, 'codigo' => 2, 'nombre' => 'Bebidas isotónicas o deportivas, a que se refiere la fracción arancelaria 2202.90.90', 'nombre_corto' => 'Bebidas isotónicas o deportivas Q0.12', 'tasa_monto' => 0.12, 'id_cvimpuestostipo' => 11],
            ['id' => 37, 'codigo' => 3, 'nombre' => 'Jugos y néctares naturales o de fruta natural y jugos artificiales a los que se refiere la partida arancelaria 2009, y la fracción arancelaria 2202.90.90.', 'nombre_corto' => 'Jugos y néctares Q0.10', 'tasa_monto' => 0.10, 'id_cvimpuestostipo' => 11],
            ['id' => 38, 'codigo' => 4, 'nombre' => 'Bebidas de yogur de cualquier clase, a que se refiere la fracción arancelaria 0403.10.00', 'nombre_corto' => 'Bebidas de yogur Q0.10', 'tasa_monto' => 0.10, 'id_cvimpuestostipo' => 11],
            ['id' => 39, 'codigo' => 5, 'nombre' => 'Agua natural envasada, a que se refiere la partida arancelaria 2201, en envases de hasta cuatro litros. Queda exceptuada del impuesto el agua natural envasada en envases de más de cuatro litros, que se utiliza para uso doméstico.', 'nombre_corto' => 'Agua natural envasada Q0.08', 'tasa_monto' => 0.08, 'id_cvimpuestostipo' => 11],
            ['id' => 40, 'codigo' => 1, 'nombre' => 'Tarifa portuaria', 'nombre_corto' => 'Tarifa Portuaria $0.05', 'tasa_monto' => 0.05, 'id_cvimpuestostipo' => 12],
            ['id' => 41, 'codigo' => 1, 'nombre' => 'EXTRA PUESTA PARA facturas sin impuestos', 'nombre_corto' => '-', 'tasa_monto' => 0.00, 'id_cvimpuestostipo' => 13],
        ];

        ImpuestoUnidadGravable::insert($impuestosUnidadGravable);
    }
}
