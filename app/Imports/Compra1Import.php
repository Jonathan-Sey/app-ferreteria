<?php

namespace App\Imports;

use App\Models\PurchaseInvoice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Log;

class Compra1Import implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Procesar todas las fechas importantes
        $fechahoraEmision = $this->parseDateTime($row['fechahora_emision'] ?? null);
        $fechahoraCertificacion = $this->parseDateTime($row['fechahora_certificacion'] ?? null);
        $createdAt = $this->parseDateTime($row['created_at'] ?? null) ?? now();
        $updatedAt = $this->parseDateTime($row['updated_at'] ?? null) ?? now();
        $deletedAt = $this->parseDateTime($row['deleted_at'] ?? null);

        // Validación adicional para fechas críticas
        if ($fechahoraEmision === null) {
            Log::error("Fecha de emisión inválida en fila ID {$row['id']}: {$row['fechahora_emision']}");
            $fechahoraEmision = now(); // Fallback solo para desarrollo, en producción deberías manejar esto diferente
        }

        return new PurchaseInvoice([
            'id' => $row['id'],
            'id_sucursal' => $row['id_sucursal'] ?? 1, // Valor por defecto si no existe
            'tipoComprobante' => $row['tipocomprobante'] ?? 1,
            'id_moneda' => $row['id_moneda'] ?? 1,
            'id_tipoDte' => $row['id_tipodte'] ?? null,
            'id_proveedor' => $row['id_proveedor'] ?? null,
            'id_receptor' => $row['id_receptor'] ?? null,
            'fechahora_emision' => $fechahoraEmision,
            'id_certificador' => $row['id_certificador'] ?? null,
            'no_autorizacion' => $row['no_autorizacion'] ?? null,
            'serie' => $row['serie'] ?? null,
            'codigo_autorizacion' => $row['codigo_autorizacion'] ?? null,
            'fechahora_certificacion' => $fechahoraCertificacion,
            'notes' => $row['notes'] ?? null,
            'estado' => $row['estado'] ?? 1,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'deleted_at' => $deletedAt,
            'created_by' => $row['created_by'] ?? null,
            'updated_by' => $row['updated_by'] ?? null,
            'deleted_by' => $row['deleted_by'] ?? null,
        ]);
    }

    /**
     * Método mejorado para parsear fechas con hora
     * Maneja:
     * - Seriales de Excel
     * - Strings en formato Y-m-d H:i:s
     * - Fechas mal formateadas (como 0202 -> 2022)
     * - Valores vacíos o nulos
     */
    private function parseDateTime($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Si es un número (serial de Excel)
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
            }
            
            // Si es una cadena de fecha
            if (is_string($value)) {
                // Normalizar la cadena (eliminar espacios, etc.)
                $value = trim($value);
                
                // Corregir años mal formateados (como 0202 -> 2022)
                if (preg_match('/^0(\d{3}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $value, $matches)) {
                    $value = '2' . $matches[1];
                }
                
                // Intentar parsear con el formato esperado
                return Carbon::createFromFormat('Y-m-d H:i:s', $value);
            }
        } catch (\Exception $e) {
            Log::warning("Error al parsear fecha/hora: {$value} - " . $e->getMessage());
            return null;
        }

        return null;
    }
}