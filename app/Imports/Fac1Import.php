<?php

namespace App\Imports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Log;

class Fac1Import implements ToModel, WithHeadingRow, WithChunkReading
{
    /**
     * Procesa cada fila del archivo y la convierte en un modelo.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $fecha = $this->parseDate($row['fechahora_emision'] ?? null);
        if ($fecha === null) {
            Log::warning("Fecha inválida en fila: " . json_encode($row));
            $fecha = Carbon::now()->format('Y-m-d');
        }

        // Normalizar todas las claves a minúsculas
        $row = array_change_key_case($row, CASE_LOWER);

        return new Venta([
            'id'                   => $row['id'],
            'id_sucursal'          => $row['id_sucursal'],
            'tipoComprobante'      => $row['tipocomprobante'],
            'id_moneda'            => $row['id_moneda'],
            'id_tipoDte'           => $row['id_tipodte'],
            'id_emisor'            => $row['id_emisor'],
            'id_cliente'           => $row['id_cliente'],
            'consumidor_final'     => $row['consumidor_final'],
            'fechahora_emision'    => $fecha,
            'certificada'          => $row['certificada'],
            'id_certificador'      => $row['id_certificador'],
            'no_autorizacion'      => $row['no_autorizacion'],
            'serie'                => $row['serie'],
            'codigo_autorizacion'  => $row['codigo_autorizacion'],
            'fechahora_certificacion' => $row['fechahora_certificacion'],
            'notes'                => $row['notes'],
            'estado'               => $row['estado'],
            'created_at'           => $row['created_at'],
            'updated_at'           => $row['updated_at'],
            'deleted_at'           => $row['deleted_at'],
            'created_by'           => $row['created_by'],
            'updated_by'           => $row['updated_by'],
            'deleted_by'           => $row['deleted_by'],
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

    /**
     * Método para parsear la fecha utilizando Carbon.
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