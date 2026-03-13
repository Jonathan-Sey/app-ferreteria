<?php

namespace App\Imports;

use App\Models\Sucursal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SucurImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Sucursal([
            'id'           => $row['id'],
            'nombre'       => $row['nombre'],
            'direccion'    => $row['direccion'],
            'telefono'     => $row['telefono'],
            'id_municipio' => $row['id_municipio'],
            'tipo'         => $row['tipo'],
            'estado'       => $row['estado'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
            'deleted_at'   => $row['deleted_at'],
            'created_by'   => $row['created_by'],
            'updated_by'   => $row['updated_by'],
            'deleted_by'   => $row['deleted_by'],
        ]);
    }
}