<?php

namespace App\Exports;

use App\Models\Venta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


use Illuminate\Support\Facades\Auth;


class reporteVentasExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
{
    protected $ventas;
    protected $sucursal;
    protected $fechaInicio;
    protected $fechaFin;
    protected $usuario;

    public function __construct($ventas, $sucursal, $fechaInicio, $fechaFin, $usuario)
    {
        $this->ventas = $ventas;
        $this->sucursal = $sucursal;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->usuario = $usuario;
    }

    public function collection()
    {
        return $this->ventas->flatMap(function ($venta) {
            return $venta->items->map(function ($item) use ($venta) {
                return [
                    'ID Venta' => $venta->id,
                    'Fecha' => $venta->fechahora_emision,
                    'Producto' => $item->producto->nombre,
                    'Cliente' => $venta->cliente->nombre,
                    'Cantidad' => $item->cantidad,
                    'Precio Unitario' => $item->precio_unitario,
                    'Descuento' => $item->descuento,
                    'Total' => $item->total,
                ];
            });
        });
    }

    public function headings(): array
    {
        return [
            'ID Venta',
            'Fecha',
            'Producto',
            'Cliente',
            'Cantidad',
            'Precio Unitario',
            'Descuento',
            'Total',
        ];
    }

    public function title(): string
    {
        return 'Reporte de Ventas';
    }

    public function startCell(): string
    {
        return 'A6'; // Los datos comienzan en la celda A6
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el título
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Reporte de Ventas');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Detalles del reporte
        $sheet->setCellValue('A2', 'Sucursal: ' . $this->sucursal->nombre);
        $sheet->setCellValue('A3', 'Fecha Inicio: ' . ($this->fechaInicio ?? 'N/A'));
        $sheet->setCellValue('A4', 'Fecha Fin: ' . ($this->fechaFin ?? 'N/A'));
        $sheet->setCellValue('A5', 'Generado por: ' . $this->usuario);

        // Estilo para los detalles
        $sheet->getStyle('A2:A5')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);

        // Estilo para los encabezados de la tabla
        $sheet->getStyle('A6:H6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Estilo para las filas de datos
        $sheet->getStyle('A7:H' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

        // Ajustar el ancho de las columnas
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }
}