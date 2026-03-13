<?php

namespace App\Imports;

use App\Models\InventarioStock;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new InventarioStock([
            'id'              => $row['id'],
            'id_producto'     => $row['id_producto'],
            'id_sucursal'     => $row['id_sucursal'],
            'cantidad_actual' => $row['cantidad_actual'],
            'stock_minimo'    => $row['stock_minimo'],
            'ubicacion'       => $row['ubicacion'],
            'estado'          => $row['estado'],
            'created_at'      => $row['created_at'],
            'updated_at'      => $row['updated_at'],
            'deleted_at'      => $row['deleted_at'],
            'created_by'      => $row['created_by'],
            'updated_by'      => $row['updated_by'],
            'deleted_by'      => $row['deleted_by']
        ]);
    }
}
