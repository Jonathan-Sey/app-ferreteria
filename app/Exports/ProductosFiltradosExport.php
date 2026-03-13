<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductosFiltradosExport implements FromCollection, WithHeadings, WithStyles
{
    protected $productos;

    // Constructor para recibir los productos filtrados
    public function __construct($productos)
    {
        $this->productos = $productos;
    }

    public function collection()
    {
        // Mapear los productos para incluir los nombres en lugar de los IDs
        return $this->productos->map(function ($producto) {
            return [
                'codigo' => $producto->codigo,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio_compra' => $producto->precio_compra,
                'precio_venta' => $producto->precio_venta,
                'precio_mayoreo' => $producto->precio_mayoreo,
                'marca' => $producto->marca->nombre, // Nombre de la marca
                'categoria' => $producto->categoria->nombre, // Nombre de la categoría
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Código',
            'Nombre',
            'Descripción',
            'Precio Compra',
            'Precio Venta',
            'Precio Mayoreo',
            'Marca',
            'Categoría',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Aplicar estilos a los encabezados
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'], // Color azul para el encabezado
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Aplicar estilos a las filas de datos
        $sheet->getStyle('A2:I' . ($sheet->getHighestRow()))
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

        // Ajustar el ancho de las columnas
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return [];
    }
}