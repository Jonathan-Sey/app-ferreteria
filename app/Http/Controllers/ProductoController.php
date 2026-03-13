<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\PurchaseInvoice;
use App\Models\Sucursal;
use App\Models\Venta;


use App\Exports\ProductosExport;
use App\Exports\ProductosFiltradosExport;
use App\Exports\reporteComprasExport;
use App\Exports\reporteVentasExport;

use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    public function getProductos()
    {
        try {
            $productos = Producto::select('id', 'codigo', 'nombre', 'descripcion', 'precio_compra', 'precio_venta', 'precio_mayoreo')->get();
            return response()->json($productos, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener productos', 'message' => $e->getMessage()], 500);
        }
    }

    public function reporProd(){

        
    }

    public function exportar(){
        return Excel::download(new ProductosExport, 'productos.xlsx');
    }


    public function exportarFiltrado(Request $request)
    {
        Log::info('Exportar productos filtrados', ['request' => $request->all()]);
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
        $query = Producto::with(['marca', 'categoria']);

        // Aplicar filtro de fechas si se proporcionan
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }

        // Aplicar filtro de marca si se proporciona
        if ($marcaId) {
            $query->whereHas('marca', function ($q) use ($marcaId) {
                $q->where('id', $marcaId);
            });
        }

        // Aplicar filtro de categoría si se proporciona
        if ($categoriaId) {
            $query->whereHas('categoria', function ($q) use ($categoriaId) {
                $q->where('id', $categoriaId);
            });
        }


        // Obtener los productos filtrados
        $productos = $query->get();

        // Verificar si hay productos
        if ($productos->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron productos con los filtros aplicados.');
        }

        // Exportar los productos filtrados a Excel
        return Excel::download(new ProductosFiltradosExport($productos), 'productos_filtrados.xlsx');
    }

    public function reporteComprasExcel(Request $request){
        // Validar los datos de entrada
        $request->validate([
            'sucursal' => 'required|exists:sucursales,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // Obtener los parámetros de filtro
        $sucursalId = $request->input('sucursal');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Obtener la sucursal
        $sucursal = Sucursal::find($sucursalId);

        // Iniciar la consulta
        $query = PurchaseInvoice::with(['items', 'proveedores', 'sucursal'])
            ->where('id_sucursal', $sucursalId);

        // Aplicar filtro de fechas solo si se proporcionan
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
        }

        // Obtener las compras filtradas
        $compras = $query->get();

        // Verificar si hay compras
        if ($compras->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron compras con los filtros aplicados.');
        }

        // Exportar las compras filtradas a Excel
        return Excel::download(new reporteComprasExport($compras, $sucursal, $fechaInicio, $fechaFin), 'compras_filtradas.xlsx');
    }

    public function reporteVentasExcel(Request $request){
    // Validar los datos de entrada
    $request->validate([
        'sucursal' => 'required|exists:sucursales,id',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
    ]);

    // Obtener los parámetros de filtro
    $sucursalId = $request->input('sucursal');
    $fechaInicio = $request->input('fecha_inicio');
    $fechaFin = $request->input('fecha_fin');

    // Obtener la sucursal
    $sucursal = Sucursal::find($sucursalId);

    // Obtener el usuario autenticado
    $usuario = Auth::user()->name ?? 'Invitado';

    // Iniciar la consulta
    $query = Venta::with(['items.producto', 'sucursal', 'cliente'])
        ->where('id_sucursal', $sucursalId);

    // Aplicar filtro de fechas solo si se proporcionan
    if ($fechaInicio && $fechaFin) {
        $query->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
    }

    // Obtener las ventas filtradas
    $ventas = $query->get();

    // Verificar si hay ventas
    if ($ventas->isEmpty()) {
        return redirect()->back()->with('error', 'No se encontraron ventas con los filtros aplicados.');
    }

    // Exportar a Excel
    return Excel::download(
        new reporteVentasExport($ventas, $sucursal, $fechaInicio, $fechaFin, $usuario),
        'reporte_ventas.xlsx'
    );
}

}