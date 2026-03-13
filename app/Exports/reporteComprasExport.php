<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class reporteComprasExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $compras;
    protected $sucursal;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($compras, $sucursal, $fechaInicio, $fechaFin)
    {
        $this->compras = $compras;
        $this->sucursal = $sucursal;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        return $this->compras->map(function ($compra) {
            return [
                'Proveedor' => $compra->proveedores->nombre,
                'No. Factura' => $compra->no_autorizacion,
                'Total Compras' => $compra->items->sum('total'),
                'Fecha' => $compra->fechahora_emision->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['Reporte de Compras'],
            ['Sucursal: ' . $this->sucursal->nombre],
            ['Fecha Inicio: ' . ($this->fechaInicio ?? 'N/A')],
            ['Fecha Fin: ' . ($this->fechaFin ?? 'N/A')],
            [], // Espacio en blanco
            ['Proveedor', 'No. Factura', 'Total Compras', 'Fecha'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para el título
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Estilo para la sucursal y fechas
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');
        $sheet->mergeCells('A4:D4');
        $sheet->getStyle('A2:D4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Estilo para los encabezados de la tabla
        $sheet->getStyle('A6:D6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Estilo para las filas de datos
        $sheet->getStyle('A7:D' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

        // Ajustar el ancho de las columnas
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }

    public function title(): string
    {
        return 'Reporte de Compras';
    }
}