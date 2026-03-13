<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use App\Models\Producto;

class BarCodeController extends Controller
{

    public function index(Request $request){
            // Validar los datos de entrada (campos opcionales)
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'marca' => 'nullable|exists:marcas,id',
            'categoria' => 'nullable|exists:categorias,id',
        ]);

        // Obtener los parámetros de filtro
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $marcaId = $request->input('marca');
        $categoriaId = $request->input('categoria');

        // Iniciar la consulta
        $query = Producto::query();

        // Aplicar filtro de fechas si se proporcionan
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        // Aplicar filtro de marca si se proporciona
        if ($marcaId) {
            $query->where('id_marca', $marcaId);
        }

        // Aplicar filtro de categoría si se proporciona
        if ($categoriaId) {
            $query->where('id_categorias', $categoriaId);
        }


        // Obtener los productos filtrados o todos si no hay filtros
        $productos = $query->get(['codigo', 'nombre', 'fecha', 'id']);

        // Verificar si hay productos
        if ($productos->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron productos con los filtros aplicados.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('barcode', ['productos' => $productos]);
        return $pdf->stream('barcodes.pdf');


    }

    public function barcodeindividual(Request $request, $record)
    {
        
        ini_set('memory_limit', '512M');
        $producto = Producto::findOrFail($record);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('barcode', ['productos' => [$producto]]);
        return $pdf->stream('barcode_'.$producto->codigo.'.pdf');

    }
    
}
