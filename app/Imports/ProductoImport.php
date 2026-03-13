<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class ProductoImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        // Validar y parsear fecha.
        $fecha = $this->parseDate($row['fecha'] ?? null);
        if ($fecha === null) {
            Log::warning("Fecha inválida en fila: " . json_encode($row));
            $fecha = Carbon::now()->format('Y-m-d');
        }

        return new Producto([
            'id'              => $row['id'],
            'codigo'          => $row['codigo'],
            'nombre'          => $row['nombre'],
            'descripcion'     => $row['descripcion'],
            'fecha'           => $fecha,
            'imagen'          => $row['imagen'],
            'precio_compra'   => $row['precio_compra'],
            'precio_venta'    => $row['precio_venta'],
            'precio_mayoreo'  => $row['precio_mayoreo'],
            'id_marca'        => $row['id_marca'],
            'id_categorias'   => $row['id_categorias'],
            'estado'          => $row['estado'],
            'deleted_at'      => $row['deleted_at'],
            'created_at'      => $row['created_at'],
            'updated_at'      => $row['updated_at'],
            'tipo'            => $row['tipo']
        ]);
    }

    private function parseDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            } elseif (!empty($value)) {
                return Carbon::parse($value)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::warning("Error al parsear la fecha: {$value} - " . $e->getMessage());
        }
        return null;
    }
}
