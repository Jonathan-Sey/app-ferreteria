<?php

namespace App\Imports;

use App\Models\MovimientoInventario;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Log;

class Traslado1Import implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $fecha = $this->parseDate($row['fecha'] ?? null);
        if ($fecha === null) {
            Log::warning("Fecha inválida en fila: " . json_encode($row));
            $fecha = Carbon::now()->format('Y-m-d');
        }

        return new MovimientoInventario([
            'id'           => $row['id'],
            'fecha'       => $fecha,
            'id_sucursal'    => $row['id_sucursal'],
            'tipo_movimiento'     => $row['tipo_movimiento'],
            'numero_documento' => $row['numero_documento'],
            'observaciones'    => $row['observaciones'],
            'sucursal_destino'       => $row['sucursal_destino'],
            'estado'       => $row['estado'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
            'deleted_at'   => $row['deleted_at'],
            'created_by'   => $row['created_by'],
            'updated_by'   => $row['updated_by'],
            'deleted_by'   => $row['deleted_by'],
        ]);
    }


    /**
     * Método para parsear la fecha utilizando Carbon.
     * - Si el valor es numérico (serial de Excel), se convierte a un objeto de fecha.
     * - Si es una cadena, se intenta parsear.
     * - Retorna la fecha formateada en 'Y-m-d' o NULL si falla.
     *
     * @param mixed $value
     * @return string|null
     */
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
