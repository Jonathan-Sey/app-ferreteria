<?php
namespace App\Imports;

use App\Models\MovimientoInventario;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Log;

class Ajustep1Import implements ToModel, WithHeadingRow
{
    /**
     * Lista de valores permitidos para el tipo de movimiento.
     */
    protected $allowedTipos = ['AJUSTE', 'ENTRADA', 'EGRESO'];

    /**
     * Procesa cada fila y la transforma en un modelo, filtrando solo los campos esperados.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Validar id_sucursal: se espera un número mayor a cero, sino se asigna el valor predeterminado 1.
        $idSucursal = (isset($row['id_sucursal']) && is_numeric($row['id_sucursal']) && $row['id_sucursal'] > 0)
                        ? $row['id_sucursal']
                        : 1;

        // Validar tipo_movimiento: si viene vacío o no válido, se asigna 'AJUSTE'.
        $tipoMovimiento = (isset($row['tipo_movimiento']) && !empty(trim($row['tipo_movimiento'])))
                            ? strtoupper(trim($row['tipo_movimiento']))
                            : 'AJUSTE';
        if (!in_array($tipoMovimiento, $this->allowedTipos)) {
            Log::warning("Tipo de movimiento no permitido: {$tipoMovimiento}. Se asigna 'AJUSTE'.", $row);
            $tipoMovimiento = 'AJUSTE';
        }

        // Validar y parsear fecha.
        $fecha = $this->parseDate($row['fecha'] ?? null);
        if ($fecha === null) {
            Log::warning("Fecha inválida en fila: " . json_encode($row));
            $fecha = Carbon::now()->format('Y-m-d');
        }

        // Solo mapear los campos esperados y asignar valores por defecto o null.
        $data = [
            'id'                => $row['id'] ?? null,
            'fecha'             => $fecha,
            'id_sucursal'       => $idSucursal,
            'tipo_movimiento'   => $tipoMovimiento,
            'numero_documento'  => isset($row['numero_documento']) && !empty(trim($row['numero_documento']))
                                    ? trim($row['numero_documento'])
                                    : null,
            'observaciones'     => isset($row['observaciones']) ? trim($row['observaciones']) : null,
            'sucursal_destino'  => isset($row['sucursal_destino']) ? trim($row['sucursal_destino']) : null,
            'estado'            => isset($row['estado']) ? trim($row['estado']) : null,
            'created_at'        => $row['created_at'] ?? Carbon::now()->toDateTimeString(),
            'updated_at'        => $row['updated_at'] ?? Carbon::now()->toDateTimeString(),
            'deleted_at'        => $row['deleted_at'] ?? null,
            'created_by'        => $row['created_by'] ?? null,
            'updated_by'        => $row['updated_by'] ?? null,
            'deleted_by'        => $row['deleted_by'] ?? null,
        ];

        return new MovimientoInventario($data);
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