<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VendedorImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'id'                => $row['id'],
            'nombre1'           => $row['nombre1'],
            'nombre2'           => $row['nombre2'],
            'nombre3'           => $row['nombre3'],
            'apellido1'         => $row['apellido1'],
            'apellido2'         => $row['apellido2'],
            'email'             => $row['email'],
            'email_verified_at' => $row['email_verified_at'],
            'password'          => $row['password'],
            'remember_token'    => $row['remember_token'],
            'estado'            => $row['estado'],
            'created_at'        => $row['created_at'],
            'updated_at'        => $row['updated_at'],
            'deleted_at'        => $row['deleted_at'],
            'created_by'        => $row['created_by'],
            'updated_by'        => $row['updated_by'],
            'deleted_by'        => $row['deleted_by']
        ]);
    }
}
