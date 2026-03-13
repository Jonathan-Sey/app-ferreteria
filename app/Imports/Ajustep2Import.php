<?php

namespace App\Imports;

use App\Models\MovimientoInventarioDetalle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Ajustep2Import implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MovimientoInventarioDetalle([
            'id'           => $row['id'],
            'id_movimiento'       => $row['id_movimiento'],
            'id_producto'    => $row['id_producto'],
            'cantidad'     => $row['cantidad'],
            'costo_unitario' => $row['costo_unitario'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
        ]);
    }
}
