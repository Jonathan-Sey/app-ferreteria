<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Exports\ProductosExport;
use App\Http\Controllers\DownloadPdfController;
use App\Http\Controllers\BarCodeController;



/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will 
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirige al administrador al inicio
Route::get('/', function () {
    return redirect('/admin');
});
// # Generar los permisos
// php artisan shield:generate

// # Crear un superadmin (esto te dará todos los permisos)
// php artisan shield:super-admin

// # Si necesitas regenerar los permisos en caso de cambios
// php artisan shield:install
// Ruta para obtener productos
Route::get('/productos-data', [ProductoController::class, 'getProductos'])->name('productos.data');
Route::get('/productos/exportar', [ProductoController::class, 'exportar'])->name('productos.exportar');
Route::get('/productos/pdf', [DownloadPdfController::class, 'downloadAll'])->name('productos.pdf.general');
Route::get('/{record}/productos/pdf', [DownloadPdfController::class, 'downloadOne'])->name('download.one');
Route::get('/productos/pdf/filtrado', [DownloadPdfController::class, 'downloadFiltered'])->name('productos.pdf.filtrado');
Route::get('/productos/excel/filtrado', [ProductoController::class, 'exportarFiltrado'])->name('productos.excel.filtrado');
Route::get('/inventario-stocks/kardex', [DownloadPdfController::class, 'kardexGeneral'])->name('inventario.stocks.kardex');
Route::get('/inventario-stocks/kardex/{record}', [DownloadPdfController::class, 'kardexIndividual'])->name('inventario.individual.kardex');
Route::get('/Ventas/pdf/{record}', [DownloadPdfController::class, 'ventasPdf'])->name('download.ventas.pdf');
Route::get('/cotizacions/pdf/{record}', [DownloadPdfController::class, 'cotizacionPdf'])->name('download.cotizacion.pdf');
Route::get('/movimiento-inventarios/pdf/{record}', [DownloadPdfController::class, 'movimientounico'])->name('movimientos.pdf.individual');
Route::get('/movimientos/pdf/filtrado', [DownloadPdfController::class, 'generarPdfFiltrado'])->name('movimientos.pdf.filtrado');
Route::get('/purchase-invoices/reporte/pdf', [DownloadPdfController::class, 'reporteCompras'])->name('reporte.compras');
Route::get('/purchase-invoices/reporte/excel', [ProductoController::class, 'reporteComprasExcel'])->name('reporte.compras.excel');
Route::get('/ventas/reporte/pdf', [DownloadPdfController::class, 'reporteVentas'])->name('reporte.ventas');
Route::get('/ventas/reporte/excel', [ProductoController::class, 'reporteVentasExcel'])->name('reporte.ventas.excel');
Route::get('/purchase-invoices/productoscomprados/pdf', [DownloadPdfController::class, 'productosComprados'])->name('productos.comprados');
Route::get('/ventas/resumen/pdf', [DownloadPdfController::class, 'resumenVentas'])->name('resumen.ventas');

Route::get('/libro-ventas-pdf', [DownloadPdfController::class, 'libroVentasPDF'])->name('libro.ventas.pdf');
Route::get('/corte-caja/pdf', [DownloadPdfController::class, 'generarPdfCorteCaja'])->name('corte.caja.pdf');

Route::get('/reporte-utilidades/reporte/pdf', [DownloadPdfController::class, 'reporteUtilidades'])->name('reporte.utilidades.pdf');
Route::get('/purchase-invoices/librocompras/pdf', [DownloadPdfController::class, 'libroCompras'])->name('libro.compras.pdf');


//Rutas para el codigo de barras
Route::get('/productos/barcode', [BarCodeController::class, 'index'])->name('productos.codebar.all');
Route::get('/productos/barcode/{record}', [BarCodeController::class, 'barcodeindividual'])->name('productos.codebar.individual');

//Nuevas rutas para la generacion de PDF
Route::get('/purchase-invoices/{record}', [DownloadPdfController::class, 'Compra'])->name('compra.individual');
Route::get('/inventario-stocks/pedido', [DownloadPdfController::class, 'pedido'])->name('pedido.pdf');



