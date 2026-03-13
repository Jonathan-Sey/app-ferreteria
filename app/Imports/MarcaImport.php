<?php

namespace App\Imports;

use App\Models\Marca;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Log;

class MarcaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Marca([
            'id'           => $row['id'],
            'nombre'       => $row['nombre'],
            'descripcion'    => $row['descripcion'],
            'deleted_at'   => $row['deleted_at'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
            
        ]);
    }
}
