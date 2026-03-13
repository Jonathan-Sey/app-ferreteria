<?php

namespace App\Imports;

use App\Models\VentaItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Fac2Import implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new VentaItem([
            'id'           => $row['id'],
            'id_venta'       => $row['id_venta'],
            'numerolinea'    => $row['numerolinea'],
            'cantidad'     => $row['cantidad'],
            'producto_id' => $row['producto_id'],
            'precio_unitario'    => $row['precio_unitario'],
            'precio_parcial'       => $row['precio_parcial'],
            'descuento'       => $row['descuento'],
            'otros_descuentos'       => $row['otros_descuentos'],
            'total'       => $row['total'],
            'impuesto'       => $row['impuesto'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
            'deleted_at'   => $row['deleted_at'],
        ]);
    }

    /**
     * Define el tamaño del "chunk" para procesar el archivo en partes.
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000; // Procesa 1000 filas por vez
    }
}
