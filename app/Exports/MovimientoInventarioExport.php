<?php

namespace App\Exports;

use App\Models\MovimientoInventario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class MovimientoInventarioExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    protected $movimiento;

    public function __construct(MovimientoInventario $movimiento)
    {
        $this->movimiento = $movimiento;
    }

    public function collection()
    {
        // Obtener los detalles del movimiento
        $detalles = $this->movimiento->detalles;

        // Formatear los datos para el Excel
        return $detalles->map(function ($detalle) {
            return [
                'Producto' => $detalle->producto->nombre,
                'Código' => $detalle->producto->codigo,
                'Cantidad' => $detalle->cantidad,
                'Costo Unitario' => $detalle->costo_unitario,
                'Observaciones' => $this->movimiento->observaciones,
            ];
        });
    }

    public function headings(): array
    {
        // Encabezados principales
        return [
            ['FerreteriaLaPaz'], // Título
            ['Fecha', $this->movimiento->fecha], // Fecha
            ['Sucursal', $this->movimiento->sucursal->nombre], // Sucursal
            ['Tipo de Movimiento', $this->movimiento->tipo_movimiento], // Tipo de movimiento
            ['Número de Documento', $this->movimiento->numero_documento], // Número de documento
            ['Observaciones', $this->movimiento->observaciones], // Observaciones
            [], // Espacio en blanco
            ['Producto', 'Código', 'Cantidad', 'Costo Unitario', 'Observaciones'], // Encabezados de la tabla
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Aplicar estilos al archivo Excel
        return [
            // Estilo para el título
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => 'center'],
            ],
            // Estilo para los encabezados principales
            'A2:B6' => [
                'font' => ['bold' => true],
            ],
            // Estilo para los encabezados de la tabla
            8 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9D9D9']],
            ],
            // Ajustar el ancho de las columnas
            'A' => ['width' => 30],
            'B' => ['width' => 20],
            'C' => ['width' => 15],
            'D' => ['width' => 15],
            'E' => ['width' => 30],
        ];
    }

    public function columnWidths(): array
    {
        // Definir el ancho de las columnas
        return [
            'A' => 30,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 30,
        ];
    }
}