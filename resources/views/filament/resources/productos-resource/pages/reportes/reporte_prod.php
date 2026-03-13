<?php

// Asegúrate de incluir las dependencias necesarias
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductosExport;
use FPDF;

$condi = $_POST['condi'];

switch ($condi) {
    case 'xlsx':
        // Exportar a Excel
        exportarExcel();
        break;
    
    case 'pdf':
        // Exportar a PDF
        exportarPDF();
        break;
}

function exportarExcel() {
    // Usar Laravel Excel para exportar a Excel
    return Excel::download(new ProductosExport, 'productos.xlsx');
}

function exportarPDF() {
    // Usar FPDF para generar un archivo PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Título de la tabla
    $pdf->Cell(40, 10, 'ID', 1);
    $pdf->Cell(40, 10, 'Codigo', 1);
    $pdf->Cell(60, 10, 'Nombre', 1);
    $pdf->Cell(80, 10, 'Descripcion', 1);
    $pdf->Cell(40, 10, 'Precio Venta', 1);
    $pdf->Ln();

    // Obtener los productos de la base de datos
    $productos = Producto::select('id', 'codigo', 'nombre', 'descripcion', 'precio_venta')->get();
    
    foreach ($productos as $producto) {
        $pdf->Cell(40, 10, $producto->id, 1);
        $pdf->Cell(40, 10, $producto->codigo, 1);
        $pdf->Cell(60, 10, $producto->nombre, 1);
        $pdf->Cell(80, 10, $producto->descripcion, 1);
        $pdf->Cell(40, 10, $producto->precio_venta, 1);
        $pdf->Ln();
    }

    // Salida del PDF
    $pdf->Output();
}
?>
