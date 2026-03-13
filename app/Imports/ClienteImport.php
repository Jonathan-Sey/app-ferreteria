<?php

namespace App\Imports;

use App\Models\Entidad;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ClienteImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Entidad([
            'id'           => $row['id'],
            'tipo_entidad'       => $row['tipo_entidad'],
            'codigo_interno'    => $row['codigo_interno'],
            'id_afiliacion_iva'     => $row['id_afiliacion_iva'],
            'cod_establecimiento' => $row['cod_establecimiento'],
            'correo'    => $row['correo'],
            'nit'       => $row['nit'],
            'telefono'       => $row['telefono'],
            'nombre_comercial'       => $row['nombre_comercial'],
            'nombre'       => $row['nombre'],
            'direccion'       => $row['direccion'],
            'codigo_postal'       => $row['codigo_postal'],
            'id_municipio'       => $row['id_municipio'],
            'es_cliente'       => $row['es_cliente'],
            'es_proveedor'       => $row['es_proveedor'],
            'es_empresa'       => $row['es_empresa'],
            'estado'       => $row['estado'],
            'created_by'   => $row['created_by'],
            'updated_by'   => $row['updated_by'],
            'deleted_by'   => $row['deleted_by'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
            'deleted_at'   => $row['deleted_at'],
        ]);
    }
}
