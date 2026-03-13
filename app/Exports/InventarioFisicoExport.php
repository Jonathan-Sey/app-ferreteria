<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InventarioFisicoExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $result = [];

        foreach ($this->data as $sucursal) {
            // Agregar el nombre de la sucursal como una fila
            $result[] = ['EMPRESA:', $sucursal['sucursal']];
            $result[] = []; // Espacio en blanco

            // Agregar los encabezados de la tabla
            $result[] = ['Código Producto', 'Nombre del producto', 'Computo', 'Conteo FI', 'Diferencia', 'Comentario'];

            // Agregar los productos de la sucursal
            foreach ($sucursal['productos'] as $producto) {
                $result[] = [
                    $producto['codigo'],
                    $producto['nombre'],
                    $producto['computo'],
                    $producto['conteo_fi'],
                    $producto['diferencia'],
                    $producto['comentario'],
                ];
            }

            // Agregar dos espacios en blanco entre sucursales
            $result[] = [];
            $result[] = [];
        }

        return $result;
    }

    public function headings(): array
    {
        // Los encabezados se manejan en el método array()
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Aplicar estilos a la hoja de Excel
        $lastRow = $sheet->getHighestRow(); // Obtener la última fila

        // Estilo para el nombre de la empresa y sucursal
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Centrar todo el contenido de la tabla
        $sheet->getStyle('A3:F' . $lastRow)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        // Definir el ancho de las columnas
        return [
            'A' => 15, // Código Producto
            'B' => 40, // Nombre del producto
            'C' => 12, // Computo
            'D' => 12, // Conteo FI
            'E' => 12, // Diferencia
            'F' => 20, // Comentario
        ];
    }
}