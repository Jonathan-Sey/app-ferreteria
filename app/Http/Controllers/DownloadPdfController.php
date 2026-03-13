<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\InventarioStock;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\Entidad;
use App\Models\Cliente;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Cotizacion;
use App\Models\CotizacionItem;
use App\Models\MovimientoInventario;
use App\Models\MovimientoInventarioDetalle;
use App\Models\User;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;
use App\Invoices\CustomInvoiceItem;

use FPDF;

use App\Models\MetodoPago; // Import agregado
use App\Models\VentaMetodoPagoPivot;


use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\Seller;
//use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use App\Classes\ExtendedInvoiceItem; // Importar la clase extendida
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class DownloadPdfController extends Controller
{



    public function downloadAll()
    {


        
         // Obtener todos los productos
         $productos = Producto::with(['marca', 'categoria'])->get();

         $totalProductos = $productos->count();
         $totalCompra = $productos->sum('precio_compra');
         $totalVenta = $productos->sum('precio_venta');
         $totalMayoreo = $productos->sum('precio_mayoreo');


         // Crear items para la factura
         $items = $productos->map(function ($producto) use ($totalProductos,$totalCompra,$totalVenta,$totalMayoreo) {  
             return ExtendedInvoiceItem::make($producto->nombre)
                ->description("Código: {$producto->codigo} | Fecha: {$producto->fecha}")
                ->quantity($producto->precio_compra)
                ->pricePerUnit($producto->precio_venta)
                ->discount($producto->precio_mayoreo)
                ->marca($producto->marca->nombre)
                ->units($producto->presentacion->nombre)
                ->addCustomField($producto->categoria->nombre)
                ->TotalPro($totalProductos)
                ->totalCompras($totalCompra)
                ->totalVentas($totalVenta)
                ->totalMayoreos($totalMayoreo);
                //->addCustomField($producto->id_categoria);
                
                //->taxByPercent($producto->id_categoria);
                
                //->addCustomField($producto->precio_mayoreo);

         });

         // Crear la factura (PDF)
         $invoice = Invoice::make()
            ->sequence(Carbon::now()->format('Ymd')) // Número secuencial basado en la fecha
            ->date(Carbon::now()) // Fecha actual
            ->buyer(new Buyer([
                'name' => 'Reporte de Productos',
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            
            ->addItems($items->toArray());
            
             //->filename('reporte_productos.pdf')
             //->template('invoice.default');
 
         // Retornar el PDF para descarga
         return $invoice->stream();
    }



    public function downloadOne(Producto $record){
            // Obtener el producto específico con sus relaciones
        $producto = Producto::with(['marca', 'categoria'])->find($record->id);

        // Verificar si el producto existe
        if (!$producto) {
            return redirect()->back()->with('error', 'Producto no encontrado.');
        }

        // Crear un solo item para la factura
        $item = ExtendedInvoiceItem::make($producto->nombre)
            ->description("Código: {$producto->codigo} | Fecha: {$producto->fecha}")
            ->quantity($producto->precio_compra)
            ->pricePerUnit($producto->precio_venta)
            ->discount($producto->precio_mayoreo)
            ->marca($producto->marca->nombre)
            ->addCustomField($producto->categoria->nombre);

        // Crear la factura (PDF)
        $invoice = Invoice::make()
            ->sequence(Carbon::now()->format('Ymd')) // Número secuencial basado en la fecha
            ->date(Carbon::now()) // Fecha actual
            ->buyer(new Buyer([
                'name' => 'Reporte de Producto Individual',
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItem($item); // Agregar el único item

        // Retornar el PDF para descarga
        return $invoice->stream();
    }


    public function downloadFiltered(Request $request){
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
            // Redirigir con un mensaje de error
            return redirect()->back()->with('error', 'No se encontraron productos con los filtros aplicados.');
        }

        // Calcular totales
        $totalProductos = $productos->count();
        $totalCompra = $productos->sum('precio_compra');
        $totalVenta = $productos->sum('precio_venta');
        $totalMayoreo = $productos->sum('precio_mayoreo');

        // Crear items para la factura
        $items = $productos->map(function ($producto) use ($totalProductos, $totalCompra, $totalVenta, $totalMayoreo) {
            return ExtendedInvoiceItem::make($producto->nombre)
                ->description("Código: {$producto->codigo} | Fecha: {$producto->fecha}")
                ->quantity($producto->precio_compra)
                ->pricePerUnit($producto->precio_venta)
                ->discount($producto->precio_mayoreo)
                ->marca($producto->marca->nombre)
                ->units($producto->presentacion->nombre)
                ->addCustomField($producto->categoria->nombre)
                ->TotalPro($totalProductos)
                ->totalCompras($totalCompra)
                ->totalVentas($totalVenta)
                ->totalMayoreos($totalMayoreo);
        });

        // Crear la factura (PDF)
        $invoice = Invoice::make()
            ->sequence(Carbon::now()->format('Ymd')) // Número secuencial basado en la fecha
            ->date(Carbon::now()) // Fecha actual
            ->buyer(new Buyer([
                'name' => 'Reporte de Productos Filtrados',
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items->toArray());

        // Retornar el PDF para descarga
        return $invoice->stream();
    }



    public function kardexGeneral(Request $request){
        // Obtener parámetros de filtro
        $productoId = $request->input('producto');
        $sucursalId = $request->input('sucursal');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Obtener información básica
        $producto = Producto::find($productoId);
        $sucursal = Sucursal::find($sucursalId);
        $inventario = InventarioStock::where('id_producto', $productoId)
                            ->where('id_sucursal', $sucursalId)
                            ->first();

        // Obtener todas las transacciones
        $compras = PurchaseInvoiceItem::with('purchaseInvoice')->where('producto_id', $productoId)
            ->whereHas('purchaseInvoice', function($query) use ($sucursalId, $fechaInicio, $fechaFin) {
                $query->where('id_sucursal', $sucursalId);
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
                }
            })->get();

        $ventas = VentaItem::with('venta')->where('producto_id', $productoId)
            ->whereHas('venta', function($query) use ($sucursalId, $fechaInicio, $fechaFin) {
                $query->where('id_sucursal', $sucursalId);
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
                }
            })->get();

        $movimientos = MovimientoInventarioDetalle::with('movimientoInventario')
            ->where('id_producto', $productoId)
            ->whereHas('movimientoInventario', function($query) use ($sucursalId, $fechaInicio, $fechaFin) {
                $query->where('id_sucursal', $sucursalId);
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
                }
            })->get();

        // Preparar movimientos consolidados
        $kardex = collect();

        // Procesar compras
        foreach ($compras as $compra) {
            $kardex->push([
                'fecha' => $compra->purchaseInvoice->fechahora_emision,
                'ingreso' => $compra->cantidad,
                'egreso' => 0,
                'tipo' => 'COMPRA',
                'concepto' => 'Compra #'.$compra->purchaseInvoice->id,
                'stock' => 0
            ]);
        }

        // Procesar ventas
        foreach ($ventas as $venta) {
            $kardex->push([
                'fecha' => $venta->venta->fechahora_emision,
                'ingreso' => 0,
                'egreso' => $venta->cantidad,
                'tipo' => 'VENTA',
                'concepto' => 'Venta #'.$venta->venta->id,
                'stock' => 0
            ]);
        }

        // Procesar movimientos de inventario (incluyendo ENTRADAS)
        foreach ($movimientos as $mov) {
            $tipo = $mov->movimientoInventario->tipo_movimiento;
            $concepto = match($tipo) {
                'AJUSTE' => 'Ajuste de inventario',
                'TRASLADO' => 'Traslado a sucursal',
                'ENTRADA' => 'Entrada directa',
                default => $mov->movimientoInventario->observaciones
            };

            $kardex->push([
                'fecha' => $mov->movimientoInventario->fecha,
                'ingreso' => in_array($tipo, ['AJUSTE', 'ENTRADA']) ? $mov->cantidad : 0,
                'egreso' => in_array($tipo, ['SALIDA', 'TRASLADO']) ? $mov->cantidad : 0,
                'tipo' => $tipo,
                'concepto' => $concepto,
                'stock' => 0
            ]);
        }

        // Ordenar por fecha y calcular stock acumulado
        $kardex = $kardex->sortBy('fecha');
        $stockAcumulado = 0;
        $kardexFinal = [];

        foreach ($kardex as $mov) {
            $stockAcumulado += $mov['ingreso'] - $mov['egreso'];
            $kardexFinal[] = [
                'fecha' => $mov['fecha'],
                'ingreso' => $mov['ingreso'],
                'egreso' => $mov['egreso'],
                'tipo' => $mov['tipo'],
                'concepto' => $mov['concepto'],
                'stock' => $stockAcumulado
            ];
        }

        // Generar PDF manteniendo tu campo personalizado
        $items = collect($kardexFinal)->map(function($item) use ($inventario) {
            return ExtendedInvoiceItem::make($item['stock'])
                ->Fecha(Carbon::parse($item['fecha'])->format('d/m/Y'))
                ->Ingreso($item['ingreso'])
                ->Egreso($item['egreso'])
                ->Tipo($item['tipo'])
                ->Concepto($item['concepto'])
                ->Stock($item['stock'])
                ->StockIndividual($inventario ? $inventario->cantidad_actual : 0)
                ->quantity(0)
                ->pricePerUnit(0)
                ->discount(0);
        });

        $invoice = Invoice::make()
            ->template('kardex')
            ->sequence(now()->format('Ymd'))
            ->date(now())
            ->buyer(new Buyer(['name' => 'Reporte de Kardex']))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->name("SMARTZON")
            ->seller(new Party(['name' => $producto->nombre ?? 'Producto']))
            ->buyer(new Party(['name' => $sucursal->nombre ?? 'Sucursal']))
            ->addItems($items->toArray());

        return $invoice->stream();
    }


    public function kardexIndividual(InventarioStock $record, Request $request){
        // Obtener parámetros de filtro
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        
        // Obtener información del registro
        $producto = $record->producto;
        $sucursal = $record->sucursal;
        $productoId = $record->id_producto;
        $sucursalId = $record->id_sucursal;
    
        // Configurar partes para el PDF
        $NombreProducto = new Party(['name' => $producto->nombre ?? 'Producto no seleccionado']);
        $NombreSucursal = new Party(['name' => $sucursal->nombre ?? 'Sucursal no seleccionada']);
    
        // Obtener transacciones con filtros
        $compras = PurchaseInvoiceItem::with('purchaseInvoice')
            ->where('producto_id', $productoId)
            ->whereHas('purchaseInvoice', function($query) use ($sucursalId, $fechaInicio, $fechaFin) {
                $query->where('id_sucursal', $sucursalId);
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
                }
            })->get();
    
        $ventas = VentaItem::with('venta')
            ->where('producto_id', $productoId)
            ->whereHas('venta', function($query) use ($sucursalId, $fechaInicio, $fechaFin) {
                $query->where('id_sucursal', $sucursalId);
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
                }
            })->get();
    
        $movimientos = MovimientoInventarioDetalle::with('movimientoInventario')
            ->where('id_producto', $productoId)
            ->whereHas('movimientoInventario', function($query) use ($sucursalId, $fechaInicio, $fechaFin) {
                $query->where('id_sucursal', $sucursalId);
                if ($fechaInicio && $fechaFin) {
                    $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
                }
            })->get();
    
        // Preparar movimientos consolidados
        $kardex = collect();
    
        // Procesar compras
        foreach ($compras as $compra) {
            $kardex->push([
                'fecha' => $compra->purchaseInvoice->fechahora_emision,
                'ingreso' => $compra->cantidad,
                'egreso' => 0,
                'tipo' => 'COMPRA',
                'concepto' => 'Compra #'.$compra->purchaseInvoice->id,
                'stock' => 0
            ]);
        }
    
        // Procesar ventas
        foreach ($ventas as $venta) {
            $kardex->push([
                'fecha' => $venta->venta->fechahora_emision,
                'ingreso' => 0,
                'egreso' => $venta->cantidad,
                'tipo' => 'VENTA',
                'concepto' => 'Venta #'.$venta->venta->id,
                'stock' => 0
            ]);
        }
    
        // Procesar movimientos de inventario (ENTRADAS, AJUSTES, etc.)
        foreach ($movimientos as $mov) {
            $tipo = $mov->movimientoInventario->tipo_movimiento;
            $concepto = match($tipo) {
                'AJUSTE' => 'Ajuste de inventario',
                'TRASLADO' => 'Traslado a sucursal',
                'ENTRADA' => 'Entrada directa',
                default => $mov->movimientoInventario->observaciones
            };
    
            $kardex->push([
                'fecha' => $mov->movimientoInventario->fecha,
                'ingreso' => in_array($tipo, ['AJUSTE', 'ENTRADA']) ? $mov->cantidad : 0,
                'egreso' => in_array($tipo, ['SALIDA', 'TRASLADO']) ? $mov->cantidad : 0,
                'tipo' => $tipo,
                'concepto' => $concepto,
                'stock' => 0
            ]);
        }
    
        // Ordenar por fecha
        $kardex = $kardex->sortBy('fecha');
    
        // Calcular stock acumulado
        $stockAcumulado = 0;
        $kardexFinal = [];
    
        // Solo agregar stock inicial si NO hay filtros de fecha
        if (!$fechaInicio && !$fechaFin) {
            $kardexFinal[] = [
                'fecha' => $record->created_at->format('d/m/Y'),
                'ingreso' => $record->cantidad_actual,
                'egreso' => 0,
                'tipo' => 'STOCK INICIAL',
                'concepto' => 'Existencia inicial',
                'stock' => $record->cantidad_actual
            ];
            $stockAcumulado = $record->cantidad_actual;
        }
    
        // Procesar movimientos
        foreach ($kardex as $mov) {
            $stockAcumulado += $mov['ingreso'] - $mov['egreso'];
            $kardexFinal[] = [
                'fecha' => $mov['fecha'],
                'ingreso' => $mov['ingreso'],
                'egreso' => $mov['egreso'],
                'tipo' => $mov['tipo'],
                'concepto' => $mov['concepto'],
                'stock' => $stockAcumulado
            ];
        }
    
        // Generar PDF
        $items = collect($kardexFinal)->map(function($item) use ($record) {
            return ExtendedInvoiceItem::make($item['stock'])
                ->Fecha(Carbon::parse($item['fecha'])->format('d/m/Y'))
                ->Ingreso($item['ingreso'])
                ->Egreso($item['egreso'])
                ->Tipo($item['tipo'])
                ->Concepto($item['concepto'])
                ->Stock($item['stock'])
                ->StockIndividual($record->cantidad_actual)
                ->quantity(0)
                ->pricePerUnit(0)
                ->discount(0);
        });
    
        $invoice = Invoice::make()
            ->template('kardex')
            ->sequence(now()->format('Ymd'))
            ->date(now())
            ->buyer(new Buyer(['name' => 'Reporte de Kardex Individual']))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->name("SMARTZON")
            ->seller($NombreProducto)
            ->buyer($NombreSucursal)
            ->addItems($items->toArray());
    
        return $invoice->stream();
    }


    public function ventasPdf(Venta $record){
            // Obtener el VentaItem específico con sus relaciones
        $ventaItem = VentaItem::with(['producto', 'impuestos', 'venta.cliente', 'venta.emisor'])->where('id_venta', $record->id)->first();
        $ventaSucursal = Venta::with('sucursal')->find($record->id_sucursal);

        // Verificar si el VentaItem existe
        if (!$ventaItem) {
            return redirect()->back()->with('error', 'VentaItem no encontrado.');
        }

        // Obtener la venta asociada al VentaItem
        $producto = $ventaItem->producto;
        $venta = $ventaItem->venta;
        $sucursal = $ventaSucursal->sucursal ?? 'SMARTZON';

        Log::info('Generando PDF para VentaItem ewaererere', ['venta_item_id' => $record->id]);

        //dd($venta->cliente, $venta->emisor);

        // Verificar si la venta existe
        if (!$venta) {
            return redirect()->back()->with('error', 'Venta no encontrada.');
        }
        Log::info('Generando PDF para VentaItem222', ['venta_item_id' => $record->id]);

        // Crear el comprador (cliente de la venta)
        $customer = new Party([
            'Numero de autorizacion'          => $venta->cliente->nombre, // Suponiendo que el cliente tiene un campo "name"
            'custom_fields' => [
                'No. de Certificación'=> $venta->no_autorizacion ?? 'N/A',
                'Serie'=> $venta->serie ?? 'N/A',
                'Codigo Autorización'=> $venta->codigo_autorizacion ?? 'N/A',
                'Fecha de Emision' => $venta->fechahora_emision ? \Carbon\Carbon::parse($venta->fechahora_emision)->timezone('America/Guatemala')->format('d/m/Y H:i:s') : 'N/A', // Agregar más campos si es necesario
                'Fecha y hora de Certificación' => $venta->fechahora_certificacion ?? 'N/A', // Agregar más campos si es necesario

            ],
        ]);

        Log::info('Generando PDF para VentaItemcasdfsdafd ewaererere', ['venta_item_id' => $record->id]);

        // Crear el vendedor (emisor de la venta)
        $seller = new Party([
            'name'          => $sucursal->nombre ?? 'SMARTZON', // Suponiendo que el emisor tiene un campo "name"
            // 'code' => $venta->emisor->nit ?? 'N/A',
            // 'vat' => $venta->emisor->nombre_comercial ?? 'N/A',
            //'email' => $venta->emisor->correo ?? 'N/A', // Agregar más campos si es necesario
            'address' => $sucursal->direccion ?? 'N/A',
            'phone' => $sucursal->telefono ?? 'N/A',
            'custom_fields' => [
                'NIT Receptor' => $venta->cliente->nit ?? 'N/A',
                'Nombre Receptor' => $venta->cliente->nombre ?? 'N/A',
                'Direccion Comprador' => $venta->cliente->direccion ?? 'N/A',     

            ],
        ]);

        Log::info('Generando PDF para VentaIsadfsdafsdftem ewaererere', ['venta_item_id' => $record->id]);

        // Crear los ítems de la factura basados en los VentaItem asociados a la venta
        $items = [];
        foreach ($venta->items as $item) {
            $description = "Código: {$producto->codigo}";

            // // Agregar impuestos a la descripción
            // foreach ($item->impuestos as $impuesto) {
            //     $description .= "Impuesto ({$impuesto->nombre}): Q{$impuesto->monto}\n";
            // }

            $invoiceItem = ExtendedInvoiceItem::make($item->producto->nombre)
                ->description(Str::limit($description,45)) // Agregar la descripción con impuestos
                ->Descripcion($producto->descripcion)
                ->quantity($item->cantidad)
                ->pricePerUnit($item->precio_unitario)
                ->discount($item->descuento + $item->otros_descuentos);
                

            $items[] = $invoiceItem;
        }

        // Crear la factura (PDF)
        $invoice = Invoice::make()
            ->template('factura')
            ->buyer($customer)
            ->seller($seller)
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->name("Factura");

        // Agregar todos los ítems a la factura
        foreach ($items as $item) {
            $invoice->addItem($item);
        }

        // Agregar totales de la venta (subtotal, impuestos, total) como notas
        

        // Retornar el PDF para descarga
        return $invoice->stream();
    }
    
    public function cotizacionPdf(Cotizacion $record){
            // Obtener la cotización específica con sus relaciones
        $cotizacion = Cotizacion::with(['cliente', 'sucursal', 'items.producto'])->find($record->id);

        // Verificar si la cotización existe
        if (!$cotizacion) {
            return redirect()->back()->with('error', 'Cotización no encontrada.');
        }

        // Crear el comprador (cliente de la cotización)
        $customer = new Party([
            'name' => $cotizacion->cliente->nombre ?? 'Cliente no especificado',
            'custom_fields' => [
                'Dirección' => $cotizacion->cliente->direccion ?? 'N/A',
                'Teléfono' => $cotizacion->cliente->telefono ?? 'N/A',
            ],
        ]);

        // Crear el vendedor (sucursal de la cotización)
        $seller = new Party([
            'name' => $cotizacion->cliente->nombre ?? 'SMARTZON',
            // 'address' => $cotizacion->sucursal->direccion ?? 'N/A',
            // 'phone' => $cotizacion->sucursal->telefono ?? 'N/A',
        ]);

        // Crear los ítems de la cotización
        $items = [];
        foreach ($cotizacion->items as $item) {
            $invoiceItem = ExtendedInvoiceItem::make($item->producto->nombre)
                ->quantity($item->cantidad)
                ->pricePerUnit($item->precio_unitario)
                ->discount($item->descuento);

            $items[] = $invoiceItem;
        }

        // Notas adicionales
        $notas = [
            'Agradecemos la oportunidad de presentar esta propuesta.'
        ];
        $notas = implode("<br>", $notas);

        // Crear la cotización (PDF)
        $invoice = Invoice::make()
            ->template('cotizacion')
            ->buyer($customer)
            ->seller($seller)
            ->dateFormat('d/m/Y')
            ->notes($notas)
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->name("Cotización");

        // Agregar todos los ítems a la cotización
        foreach ($items as $item) {
            $invoice->addItem($item);
        }

        // Retornar el PDF para descarga
        return $invoice->stream();
    }

     // PDF Individual
     public function movimientounico(MovimientoInventario $record)
     {
         // Obtener el movimiento de inventario específico con sus relaciones
         $movimiento = MovimientoInventario::with(['sucursal', 'sucursalDestino', 'detalles.producto'])->find($record->id);
     
         // Verificar si el movimiento existe
         if (!$movimiento) {
             return redirect()->back()->with('error', 'Movimiento de inventario no encontrado.');
         }
     
         // Crear el comprador (puede ser la sucursal de origen)
         $buyer = new Buyer([
             'name'          => $movimiento->sucursal->nombre,
             'custom_fields' => [
                 'email' => 'sucursal@example.com',
             ],
         ]);
     
         // Crear el vendedor (puede ser la sucursal de destino)
         $seller = new Party([
             'name'          => $movimiento->sucursal ? $movimiento->sucursal->nombre : 'N/A',
             'custom_fields' => [
                 'tipo_movimiento' => $movimiento->tipo_movimiento,
                 'numero_documento' => $movimiento->numero_documento,
                 'observaciones' => $movimiento->observaciones,
                 // Agregar la sucursal de destino si el tipo de movimiento es Traslado
                 'sucursal_destino' => $movimiento->tipo_movimiento === 'TRASLADO' ? $movimiento->sucursalDestino->nombre : 'N/A',
             ],
         ]);
     
         // Crear los items de la factura basados en los detalles del movimiento
         $items = [];
         foreach ($movimiento->detalles as $detalle) {
             $items[] = ExtendedInvoiceItem::make($detalle->producto->nombre)
                 ->pricePerUnit($detalle->costo_unitario)
                 ->quantity($detalle->cantidad)
                 ->description("Código: {$detalle->producto->codigo} | Fecha: {$detalle->producto->fecha}");
         }
     
         // Crear la factura (PDF)
         $invoice = Invoice::make()
             ->template('movimientos')
             ->name('SMARTZON')
             ->buyer($buyer)
             ->seller($seller)
             ->date(Carbon::now())
             ->dateFormat('d/m/Y')
             ->currencySymbol('Q')
             ->currencyCode('GTQ')
             ->addItems($items);
     
         // Retornar el PDF para descarga
         return $invoice->stream();
     }

     

     public function generarPdfFiltrado(Request $request)
     {
         // Validar los datos del formulario
         $request->validate([
             'fecha_inicio' => 'nullable|date',
             'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
             'sucursal' => 'nullable|exists:sucursales,id',
             'tipo_movimiento' => 'required|in:inventario_inicial,entrada_salida,traslados',
         ]);
     
         // Obtener los datos del formulario
         $fechaInicio = $request->input('fecha_inicio');
         $fechaFin = $request->input('fecha_fin');
         $sucursalId = $request->input('sucursal');
         $tipoMovimiento = $request->input('tipo_movimiento');
     
         // Aplicar filtros a la consulta
         $movimientos = MovimientoInventario::query();
     
         // Filtrar por tipo de movimiento
         switch ($tipoMovimiento) {
             case 'inventario_inicial':
                 $movimientos->where('tipo_movimiento', 'ENTRADA');
                 $tipoMovimientoLabel = 'Inventario inicial';
                 break;
             case 'entrada_salida':
                 $movimientos->whereIn('tipo_movimiento', ['SALIDA', 'AJUSTE']);
                 $tipoMovimientoLabel = 'Entrada y Salida (ajustes)';
                 break;
             case 'traslados':
                 $movimientos->where('tipo_movimiento', 'TRASLADO');
                 $tipoMovimientoLabel = 'Traslados';
                 break;
         }
     
         // Filtrar por sucursal si se proporciona
         if ($sucursalId) {
             $movimientos->where('id_sucursal', $sucursalId);
         }
     
         // Filtrar por rango de fechas si se proporcionan
         if ($fechaInicio && $fechaFin) {
             $movimientos->whereBetween('fecha', [$fechaInicio, $fechaFin]);
         }
     
         // Obtener los resultados
         $movimientosFiltrados = $movimientos->get();
     
         // Verificar si hay movimientos
         if ($movimientosFiltrados->isEmpty()) {
             return redirect()->back()->with('error', 'No se encontraron movimientos con los filtros aplicados.');
         }
     
         // Crear el objeto Party para el vendedor (sucursal)
         $seller = new Party([
             'name' => 'SMARTZON',
             'custom_fields' => [
                 'tipo_movimiento' => $tipoMovimientoLabel,
                 'sucursal_destino' => 'N/A', // Valor por defecto
             ],
         ]);

        // Crear el objeto Buyer para el comprador
        $buyer = new Buyer([
            'name' => 'Cliente General',
            'custom_fields' => [
                'email' => 'cliente@example.com',
            ],
        ]);

        // Crear los ítems para la factura
        $items = [];
        foreach ($movimientosFiltrados as $movimiento) {
            $items[] = ExtendedInvoiceItem::make($movimiento->fecha)
                ->description($movimiento->sucursal->nombre)
                ->pricePerUnit(0)
                ->quantity(1)
                ->units($movimiento->tipo_movimiento)
                ->discount(0)
                ->Sucursal($movimiento->observaciones)
                ->tax($movimiento->numero_documento);

            // Si el tipo de movimiento es TRASLADO, agregar la sucursal de destino
            if ($movimiento->tipo_movimiento === 'TRASLADO') {
                $seller->custom_fields['sucursal_destino'] = $movimiento->sucursal_destino ?? 'N/A';
            }
        }

        // Crear el PDF usando LaravelDaily
        $invoice = Invoice::make()
            ->name('SMARTZON')
            ->template('pdf_filtrado')
            ->seller($seller)
            ->buyer($buyer)
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items);

        // Descargar el PDF
        return $invoice->stream('movimientos_filtrados.pdf');
    }
    public function generarPdfCorteCaja(Request $request)
{
    Log::info('Iniciando generación de PDF para corte de caja', ['request' => $request->all()]);

    // Validar los datos del formulario
    $request->validate([
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'sucursal' => 'nullable|exists:sucursales,id',
        'vendedor' => 'nullable|exists:users,id',
    ]);

    // Obtener datos del filtro
    $vendedorId = $request->input('vendedor');
    $vendedor = $vendedorId ? User::find($vendedorId) : null;
    $fechaInicio = $request->input('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio'))->startOfDay() : null;
    $fechaFin = $request->input('fecha_fin') ? Carbon::parse($request->input('fecha_fin'))->endOfDay() : null;
    $sucursalId = $request->input('sucursal');

    // Consulta de ventas con relaciones necesarias
    $ventas = Venta::with(['paymentMethods.metodoPago', 'creador', 'cliente'])
        ->when($sucursalId, fn($q) => $q->where('id_sucursal', $sucursalId))
        ->when($vendedorId, fn($q) => $q->where('created_by', $vendedorId))
        ->when($fechaInicio && $fechaFin, fn($q) => $q->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]))
        ->get();

    if ($ventas->isEmpty()) {
        return redirect()->back()->with('error', 'No se encontraron ventas con los filtros aplicados.');
    }

    // Calcular totales
    $totalVentas = $ventas->sum('total');
    $totalesMetodosPago = [
        'Efectivo' => 0,
        'Tarjeta' => 0,
        'Crédito' => 0,
        'Transferencia' => 0
    ];

    foreach ($ventas as $venta) {
        foreach ($venta->paymentMethods as $pago) {
            $metodoNombre = $pago->metodoPago->nombre;
            if (isset($totalesMetodosPago[$metodoNombre])) {
                $totalesMetodosPago[$metodoNombre] += $pago->monto;
            }
        }
    }

    // Preparar items para el PDF
    $items = [];
    foreach ($ventas as $index => $venta) {
        $items[] = ExtendedInvoiceItem::make(str_pad($index + 1, 6, '0', STR_PAD_LEFT))
            ->description($venta->cliente->nombre)
            ->pricePerUnit($venta->total)
            ->quantity(1)
            ->units(Carbon::parse($venta->fechahora_emision)->timezone('America/Guatemala')->format('d/m/Y H:i'))
            ->discount(0)
            ->tax(0)
            ->Vendedor($venta->creador->nombre1 ?? 'N/A')  // Aquí está el cambio clave
            ->TotalEfectivo(100);
    }

    // Generar PDF
    $invoice = Invoice::make()
        ->name('Corte de Caja')
        ->template('corte_caja')
        ->seller(new Party([
            'name' => 'SMARTZON',
            'custom_fields' => [
                'sucursal' => $sucursalId ? Sucursal::find($sucursalId)->nombre : 'Todas',
                'fecha_inicio' => $fechaInicio?->format('d/m/Y') ?? 'N/A',
                'fecha_fin' => $fechaFin?->format('d/m/Y') ?? 'N/A',
                'vendedor' => $vendedor ? $vendedor->nombre1 : 'Todos',
                'total_efectivo' => number_format($totalesMetodosPago['Efectivo'], 2),
                'total_tarjeta' => number_format($totalesMetodosPago['Tarjeta'], 2),
                'total_credito' => number_format($totalesMetodosPago['Crédito'], 2),
                'total_transferencia' => number_format($totalesMetodosPago['Transferencia'], 2),
            ],
        ]))
        ->buyer(new Party(['name' => 'Cliente General']))
        ->dateFormat('d/m/Y')
        ->currencySymbol('Q')
        ->currencyCode('GTQ')
        ->addItems($items)
        ->totalAmount($totalVentas);

    return $invoice->stream('corte_caja.pdf');
}
    public function reporteCompras(Request $request){

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

        // Crear items para la factura
        $items = $compras->map(function ($compra) {
            return ExtendedInvoiceItem::make($compra->proveedores->nombre)
                ->NoFactura($compra->no_autorizacion)
                ->totalCompras($compra->items->sum('total'))
                ->fecha($compra->fechahora_emision->format('d/m/Y'))
                ->quantity(1)
                ->pricePerUnit($compra->items->sum('total'))
                ->discount(0);
        });

        // Crear la factura (PDF)
        $invoice = Invoice::make()
            ->name('SMARTZON')
            ->template('reportecompras')
            ->buyer(new Buyer([
                'name' => 'John Doe', // Este campo es necesario pero no se usa
            ]))
            ->seller(new Buyer([
                'name' => $sucursal->nombre,
                'custom_fields' => [
                    'Fecha Inicio' => Carbon::parse($fechaInicio)->format('d/m/Y') ?? 'N/A',
                    'Fecha Final' => Carbon::parse($fechaFin)->format('d/m/Y') ?? 'N/A',
                ],
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items->toArray());

        // Retornar el PDF para descarga
        return $invoice->stream();
    }


    public function reporteVentas(Request $request){
            // Validar los datos de entrada
        $request->validate([
            'sucursal' => 'required|exists:sucursales,id',
            'vendedor' => 'nullable|exists:users,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // Obtener los parámetros de filtro
        $sucursalId = $request->input('sucursal');
        $vendedor = $request->input('vendedor');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Obtener la sucursal
        $sucursal = Sucursal::find($sucursalId);
        $usuario = User::find($vendedor);

        // Iniciar la consulta
        $query = Venta::with(['items.producto', 'sucursal', 'creador']) // Cargar la relación items.producto
            ->where('id_sucursal', $sucursalId);

        // Aplicar filtro por vendedor si se especificó
        if ($vendedor) {
            $query->where('created_by', $vendedor);
        }

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

        // Crear items para la factura basados en encabezados de venta
        $items = $ventas->map(function ($venta) {
            return ExtendedInvoiceItem::make($venta->id)
                ->Documento($venta->tipoComprobante == 1 ? 'FEL' : ($venta->tipoComprobante == 2 ? 'NOTA' : 'N/A'))
                ->Cliente($venta->cliente->nombre)
                ->totalVentas($venta->total) // Usar el total de la venta en lugar del item
                ->fecha(Carbon::parse($venta->fechahora_emision)->timezone('America/Guatemala')->format('d/m/Y H:i:s') ?? 'N/A',)
                ->quantity(1) // Cada venta cuenta como 1 unidad
                ->pricePerUnit($venta->total) // El precio unitario es el total de la venta
                ->discount($venta->items->sum('descuento') ?? 0.00) // Sumar descuentos de todos los items
                ->Vendedor($venta->creador->nombre1 ?? 'N/A') // Agregar información del vendedor
                ->sucursal($venta->sucursal->nombre ?? 'N/A'); // Agregar información de sucursal
                //->estado($venta->estado->name ?? 'N/A'); // Agregar estado de la venta
        });

        // Crear la factura (PDF)
        $invoice = Invoice::make()
            ->name('SMARTZON')
            ->template('reporteventas') // Asegúrate de tener este template
            ->buyer(new Buyer([
                'name' => 'John Doe', // Este campo es necesario pero no se usa
            ]))
            ->seller(new Buyer([
                'name' => $sucursal->nombre,
                'custom_fields' => [
                    'Fecha Inicio' => Carbon::parse($fechaInicio)->format('d/m/Y') ?? 'N/A',
                    'Fecha Final' => Carbon::parse($fechaFin)->format('d/m/Y') ?? 'N/A',
                    'Vendedor: ' =>  ($usuario->nombre1 ?? 'Todos') ,   //Aqui es donde se pone el nombre del vendedor
                ],
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items->toArray());

        // Retornar el PDF para descarga
        return $invoice->stream();
    }

    public function productosComprados(Request $request){
    
        Log::info('Iniciando función productosComprados');

        // Validar los datos de entrada
        $request->validate([
            'sucursal' => 'required|exists:sucursales,id',
            'id_proveedor' => 'nullable|exists:entidades,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        Log::info('Datos validados correctamente');

        // Obtener los parámetros de filtro
        $sucursalId = $request->input('sucursal');
        $proveedorId = $request->input('id_proveedor');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        Log::info('Parámetros de filtro obtenidos', [
            'sucursalId' => $sucursalId,
            'proveedorId' => $proveedorId,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ]);

        // Obtener la sucursal
        $sucursal = Sucursal::find($sucursalId);
        Log::info('Sucursal obtenida', ['sucursal' => $sucursal]);

        // Iniciar la consulta
        $query = PurchaseInvoice::with(['items.productos', 'proveedores', 'sucursal'])
            ->where('id_sucursal', $sucursalId);

        Log::info('Consulta inicial construida');

        // Aplicar filtro de proveedor si se proporciona
        if ($proveedorId) {
            $query->where('id_proveedor', $proveedorId);
            Log::info('Filtro de proveedor aplicado', ['proveedorId' => $proveedorId]);
        }

        // Aplicar filtro de fechas solo si se proporcionan
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
            Log::info('Filtro de fechas aplicado', ['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin]);
        }

        // Obtener las compras filtradas y ordenar por fecha
        $compras = $query->get()->sortBy('fechahora_emision');

        Log::info('Compras obtenidas y ordenadas por fecha', ['compras' => $compras]);

        // Verificar si hay compras
        if ($compras->isEmpty()) {
            Log::warning('No se encontraron compras con los filtros aplicados');
            return redirect()->back()->with('error', 'No se encontraron compras con los filtros aplicados.');
        }

        // Agrupar compras por proveedor
        $comprasAgrupadas = $compras->groupBy('id_proveedor');

        Log::info('Compras agrupadas por proveedor', ['comprasAgrupadas' => $comprasAgrupadas]);

        // Crear items para la factura
        $items = $comprasAgrupadas->flatMap(function ($comprasPorProveedor, $proveedorId) {
            Log::info('Procesando compras del proveedor', ['proveedorId' => $proveedorId]);

            return $comprasPorProveedor->flatMap(function ($compra) {
                Log::info('Procesando compra', ['compra_id' => $compra->id]);
                return $compra->items->map(function ($item) use ($compra) {
                    Log::info('Procesando ítem', ['item_id' => $item->id, 'producto' => $item->productos]);
                    return ExtendedInvoiceItem::make($compra->proveedores->nombre)
                        ->quantity($item->cantidad)
                        ->Producto($item->productos->nombre)
                        ->fecha($compra->fechahora_emision->format('d/m/Y'))
                        ->pricePerUnit($item->precio_unitario)
                        ->discount($item->descuento ?? 0.00);
                });
            });
        });

        Log::info('Ítems para la factura creados', ['items' => $items]);

        // Crear la factura (PDF)
        $invoice = Invoice::make()
            ->name('SMARTZON')
            ->template('productoscomprados')
            ->buyer(new Buyer([
                'name' => 'John Doe', // Este campo es necesario pero no se usa
            ]))
            ->seller(new Buyer([
                'name' => $sucursal->nombre,
                'custom_fields' => [
                    'Fecha Inicio' => Carbon::parse($fechaInicio)->format('d/m/Y') ?? 'N/A',
                    'Fecha Final' => Carbon::parse($fechaFin)->format('d/m/Y') ?? 'N/A',
                ],
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items->toArray());

        Log::info('Factura creada correctamente');

        // Retornar el PDF para descarga
        return $invoice->stream();
        
    }


    public function resumenVentas(Request $request){

            Carbon::setLocale('es');

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
        $query = Venta::with(['items.producto', 'sucursal'])
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

        // Organizar las ventas por fecha
        $ventasPorFecha = [];
        foreach ($ventas as $venta) {
            $fecha = $venta->fechahora_emision->format('d/m/Y');
            $dia = Carbon::parse($venta->fechahora_emision)->isoFormat('dddd');
            $efectivo = $venta->items->sum('precio_parcial');
            $credito = $venta->credito;
            $tarjeta = $venta->tarjeta;
            $total = $efectivo + $credito + $tarjeta;

            if (!isset($ventasPorFecha[$fecha])) {
                $ventasPorFecha[$fecha] = [
                    'dia' => $dia,
                    'efectivo' => 0,
                    'credito' => 0,
                    'tarjeta' => 0,
                    'total' => 0,
                ];
            }

            $ventasPorFecha[$fecha]['efectivo'] += $efectivo;
            $ventasPorFecha[$fecha]['credito'] += $credito;
            $ventasPorFecha[$fecha]['tarjeta'] += $tarjeta;
            $ventasPorFecha[$fecha]['total'] += $total;
        }

        // Crear items para la factura
        $items = [];
        foreach ($ventasPorFecha as $fecha => $datos) {
            $items[] = ExtendedInvoiceItem::make($fecha)
                ->Dia($datos['dia'])
                ->Credito($datos['credito'])
                ->Tarjeta($datos['tarjeta'])
                ->pricePerUnit($datos['total']);
        }

        // Crear la factura (PDF)
        $invoice = Invoice::make()
            ->name('SMARTZON')
            ->template('resumenventas') // Asegúrate de tener este template
            ->buyer(new Buyer([
                'name' => 'John Doe', // Este campo es necesario pero no se usa
            ]))
            ->seller(new Buyer([
                'name' => $sucursal->nombre,
                'custom_fields' => [
                    'Fecha Inicio' => Carbon::parse($fechaInicio)->format('d/m/Y') ?? 'N/A',
                    'Fecha Final' => Carbon::parse($fechaFin)->format('d/m/Y') ?? 'N/A',
                    'Vendedor: ' =>  (Auth::user()->nombre1 ?? 'Invitado'),
                ],
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items);

        // Retornar el PDF para descarga
        return $invoice->stream();
    }

    public function reporteUtilidades(Request $request){
            // Validar los datos de entrada
        $request->validate([
            'sucursal' => 'required|exists:sucursales,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        // Obtener los parámetros de filtro
        $sucursalId = $request->input('sucursal');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Obtener la sucursal (si se filtró)
        $nombreSucursal = 'Todas las sucursales';
        if ($sucursalId) {
            $sucursal = Sucursal::find($sucursalId);
            $nombreSucursal = $sucursal->nombre;
        }

        // Consulta base para los items de venta
        $query = VentaItem::with(['venta', 'producto'])
            ->whereHas('venta', function($q) use ($fechaInicio, $fechaFin, $sucursalId) {
                $q->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);
                
                if ($sucursalId) {
                    $q->where('id_sucursal', $sucursalId);
                }
            });

        // Obtener los items filtrados
        $ventaItems = $query->get();

        // Verificar si hay datos
        if ($ventaItems->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron ventas con los filtros aplicados.');
        }

        // Calcular totales generales
        $totales = [
            'total_ventas' => 0,
            'total_compras' => 0,
            'total_ganancia' => 0,
        ];

        // Preparar items para el PDF
        $items = $ventaItems->map(function ($item) use (&$totales) {
            $precioVenta = $item->precio_unitario ?? 0;
            $precioCompra = $item->producto->precio_compra ?? 0;
            $gananciaUnidad = $precioVenta - $precioCompra;
            $gananciaTotal = $gananciaUnidad * $item->cantidad;
            $totalVenta = $precioVenta * $item->cantidad;
            $totalCompra = $precioCompra * $item->cantidad;
            $porcentaje = $precioVenta > 0 ? ($gananciaUnidad / $precioVenta) * 100 : 0;

            // Acumular totales
            $totales['total_ventas'] += $totalVenta;
            $totales['total_compras'] += $totalCompra;
            $totales['total_ganancia'] += $gananciaTotal;

            return ExtendedInvoiceItem::make($item->producto->nombre)
                ->pricePerUnit($precioVenta)
                ->Fecha($item->venta->fechahora_emision->format('d/m/Y'))
                ->quantity($item->cantidad)
                ->precioVenta($precioVenta)
                ->precioCompra($precioCompra)
                ->Ganancia($gananciaUnidad)
                ->totalVentas($totalVenta)
                ->totalCompras($totalCompra)
                ->gananciaBruta($gananciaTotal)
                ->Porcentaje(round($porcentaje, 2));
        });

        // Crear el PDF
        $invoice = Invoice::make()
            ->name('Reporte de Utilidades')
            ->template('utilidades')
            ->buyer(new Buyer([
                'name' => ' ', // Espacio en blanco necesario
            ]))
            ->seller(new Buyer([
                'name' => $nombreSucursal,
                'custom_fields' => [
                    'Fecha Inicio' => Carbon::parse($fechaInicio)->format('d/m/Y'),
                    'Fecha Final' => Carbon::parse($fechaFin)->format('d/m/Y'),
                    'Total Ventas' => number_format($totales['total_ventas'], 2) . ' GTQ',
                    'Total Compras' => number_format($totales['total_compras'], 2) . ' GTQ',
                    'Total Ganancia' => number_format($totales['total_ganancia'], 2) . ' GTQ',
                ],
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items->toArray());

            
        return $invoice->stream();
    }


    public function libroCompras(Request $request){
        // Validar los datos de entrada
    $request->validate([
        'sucursal' => 'required|exists:sucursales,id',
        'tipo' => 'required|in:bien,servicio',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
    ]);

    // Obtener parámetros del filtro
    $sucursalId = $request->input('sucursal');
    $tipo = $request->input('tipo');
    $fechaInicio = $request->input('fecha_inicio');
    $fechaFin = $request->input('fecha_fin');

    // Obtener la sucursal
    $sucursal = Sucursal::find($sucursalId);

    // Consulta para obtener las compras filtradas
    $compras = PurchaseInvoice::with(['items.productos', 'proveedores'])
        ->where('id_sucursal', $sucursalId)
        ->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin])
        ->orderBy('fechahora_emision')
        ->get();

    // Filtrar items según tipo (bien o servicio)
    $itemsCompras = $compras->flatMap(function ($compra) use ($tipo) {
        return $compra->items->filter(function ($item) use ($tipo) {
            return ($tipo === 'servicio') ? 
                ($item->productos->tipo === 'servicio') : 
                ($item->productos->tipo !== 'servicio');
        })->map(function ($item) use ($compra) {
            $total = $item->total;
            $iva = $item->impuesto;
            $baseImponible = $total - $iva; // Calculamos la base sin IVA
            
            // Determinar si es compra (bien) o servicio
            $compraValue = ($item->productos->tipo === 'servicio') ? 0 : $baseImponible;
            $servicioValue = ($item->productos->tipo === 'servicio') ? $baseImponible : 0;

            return [
                'fecha' => $compra->fechahora_emision->format('d/m/Y'),
                'documento' => $compra->no_autorizacion,
                'descripcion' => $item->productos->nombre,
                'compra' => $compraValue,
                'servicio' => $servicioValue,
                'iva' => $iva,
                'total' => $total,
                'proveedor' => $compra->proveedores->nombre
            ];
        });
    });

    // Validar si hay items para mostrar
    if ($itemsCompras->isEmpty()) {
        return response()->view('errors.no-items', [
            'message' => 'No se encontraron items para facturar con los filtros aplicados.',
            'periodo' => Carbon::parse($fechaInicio)->format('d/m/Y').' - '.Carbon::parse($fechaFin)->format('d/m/Y'),
            'tipo' => $tipo === 'bien' ? 'Bienes' : 'Servicios',
            'sucursal' => $sucursal->nombre
        ], 404);
    }

    // Calcular totales
    $totales = [
        'compras' => $itemsCompras->sum('compra'),
        'servicios' => $itemsCompras->sum('servicio'),
        'iva' => $itemsCompras->sum('iva'),
        'total' => $itemsCompras->sum('total'),
    ];

    // Crear items para el PDF
    $items = $itemsCompras->map(function ($item, $index) {
        return ExtendedInvoiceItem::make($index + 1)
            ->Fecha($item['fecha'])
            ->documento($item['documento'])
            ->Producto($item['descripcion'])
            ->pricePerUnit($item['compra']) // Base imponible (sin IVA) para bienes
            ->Servicio($item['servicio'])   // Base imponible (sin IVA) para servicios
            ->tax($item['iva']);            // IVA
    });

    // Crear el PDF
    $invoice = Invoice::make()
        ->name('Libro de Compras')
        ->buyer(new Buyer(['name' => ' ']))
        ->seller(new Buyer([
            'name' => $sucursal->nombre,
            'custom_fields' => [
                'Fecha Inicio' => Carbon::parse($fechaInicio)->format('d/m/Y'),
                'Fecha Final' => Carbon::parse($fechaFin)->format('d/m/Y'),
                'Tipo' => $tipo === 'bien' ? 'Bienes' : 'Servicios',
                'Total Compras' => number_format($totales['compras'], 2),
                'Total Servicios' => number_format($totales['servicios'], 2),
                'Total IVA' => number_format($totales['iva'], 2),
                'Total General' => number_format($totales['total'], 2),
            ],
        ]))
        ->template('librocompras')
        ->dateFormat('d/m/Y')
        ->currencySymbol('Q')
        ->currencyCode('GTQ')
        ->addItems($items->toArray());

    return $invoice->stream();
    }

    
    public function libroVentasPDF(Request $request){
        // Validación (se mantiene igual)
        $request->validate([
            'sucursal' => 'required|exists:sucursales,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_producto' => 'required|in:bien,servicio,todos'
        ]);

        // Obtener parámetros (se mantiene igual)
        $sucursalId = $request->input('sucursal');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $tipoProducto = $request->input('tipo_producto');

        // Obtener la sucursal (se mantiene igual)
        $sucursal = Sucursal::find($sucursalId);

        // Consulta base (se mantiene igual)
        $query = Venta::with(['items.producto', 'cliente', 'sucursal'])
            ->where('id_sucursal', $sucursalId)
            ->whereBetween('fechahora_emision', [$fechaInicio, $fechaFin]);

        if ($tipoProducto != 'todos') {
            $query->whereHas('items.producto', function($q) use ($tipoProducto) {
                $q->where('tipo', $tipoProducto);
            });
        }

        $ventas = $query->orderBy('fechahora_emision')->get();

        if ($ventas->isEmpty()) {
            return redirect()->back()->with('error', 'No se encontraron ventas con los filtros aplicados.');
        }

        // Preparar datos para el PDF - VERSIÓN CORREGIDA
        $items = [];
        $totalPrecio = 0;
        $totalGeneral = 0;
        
        foreach ($ventas as $venta) {
            $precio = $venta->total; // Usamos el total como precio para cada venta
            $totalPrecio += $precio;
            $totalGeneral += $precio; // Como servicios e IVA son 0, el total es igual al precio
            
            $items[] = (new ExtendedInvoiceItem())
                ->title('Venta #' . $venta->id)
                ->pricePerUnit($precio) // Aquí va el valor numérico para cálculos
                ->quantity(1) // Cada venta cuenta como 1
                ->description($venta->cliente->nombre ?? $venta->consumidor_final) // Cliente
                ->Fecha($venta->fechahora_emision->format('d/m/Y H:i'))
                ->Precio('Q '.number_format($precio, 2)) // Formateado para mostrar
                ->Servicios('Q 0.00')
                ->Iva('Q 0.00')
                ->Total('Q '.number_format($precio, 2)); // Igual al precio en este caso
        }

        // Agregar fila de totales
        $items[] = (new ExtendedInvoiceItem())
            ->title('TOTAL GENERAL')
            ->pricePerUnit(0)
            ->quantity(1)
            ->description('')
            ->Fecha('')
            ->Precio('Q '.number_format($totalPrecio, 2))
            ->Servicios('Q 0.00')
            ->Iva('Q 0.00')
            ->Total('Q '.number_format($totalGeneral, 2));

        // Crear el PDF (se mantiene igual)
        $invoice = Invoice::make()
            ->name('SMARTZON')
            ->template('libroventas')
            ->buyer(new Buyer([
                'name' => 'CONSOLIDADO',
            ]))
            ->seller(new Buyer([
                'name' => $sucursal->nombre,
                'custom_fields' => [
                    'Período' => Carbon::parse($fechaInicio)->format('d/m/Y').' - '.Carbon::parse($fechaFin)->format('d/m/Y'),
                    'Tipo' => $tipoProducto == 'bien' ? 'BIENES' : ($tipoProducto == 'servicio' ? 'SERVICIOS' : 'TODOS'),
                    'Sucursal' => $sucursal->nombre,
                    'Generado' => Carbon::now()->format('d/m/Y H:i'),
                    'Usuario' => Auth::user()->name ?? 'SISTEMA'
                ],
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items);

        return $invoice->stream();
    }

    public function Compra (Request $request, $record){
        // Validar el ID del registro
        $record = intval($record);
        if ($record <= 0) {
            return redirect()->back()->with('error', 'ID de registro inválido.');
        }

        // Obtener la compra por ID
        $compra = PurchaseInvoice::find($record);
        if (!$compra) {
            return redirect()->back()->with('error', 'Compra no encontrada.');
        }

        // Crear los ítems para la factura
        $items = $compra->items->map(function ($item) {
            return ExtendedInvoiceItem::make($item->productos->nombre)
                ->description(Str::limit($item->productos->descripcion, 50))
                ->pricePerUnit($item->precio_unitario)
                ->quantity($item->cantidad)
                ->totalCompras($item->total)
                ->fecha($item->created_at->format('d/m/Y'))
                ->discount($item->descuento ?? 0.00);
        });

        // Crear el PDF usando LaravelDaily
        $invoice = Invoice::make()
            ->name('Compra Detallada')
            ->template('compradetallada')
            ->buyer(new Buyer([
                'name' => $compra->proveedores->nombre,
            ]))
            ->seller(new Buyer([
                'name' => 'SMARTZON',
                'custom_fields' => [
                    'Fecha' => $compra->fechahora_emision->format('d/m/Y'),
                    'No. Autorización' => $compra->no_autorizacion,
                    'Sucursal' => $compra->sucursal->nombre,
                ],
            ]))
            ->dateFormat('d/m/Y')
            ->currencySymbol('Q')
            ->currencyCode('GTQ')
            ->addItems($items);

        // Descargar el PDF
        return $invoice->stream('compra_detallada.pdf');
    }

    public function pedido(Request $request){
        // Validar los datos recibidos del formulario
    $request->validate([
        'sucursal' => 'required|exists:sucursales,id',
        'cantidad' => 'required|numeric|min:1|max:9999',
        'marca' => 'nullable|exists:marcas,id',
        'categoria' => 'nullable|exists:categorias,id',
    ]);

    $sucursalId = $request->input('sucursal');
    $cantidadMax = $request->input('cantidad');
    $marcaId = $request->input('marca');
    $categoriaId = $request->input('categoria');

    // Consulta de inventario filtrando por sucursal y cantidad
    $query = \App\Models\InventarioStock::with(['producto.marca', 'producto.categoria'])
        ->where('id_sucursal', $sucursalId)
        ->where('cantidad_actual', '<=', $cantidadMax);

    // Filtros opcionales
    if ($marcaId) {
        $query->whereHas('producto.marca', function ($q) use ($marcaId) {
            $q->where('id', $marcaId);
        });
    }
    if ($categoriaId) {
        $query->whereHas('producto.categoria', function ($q) use ($categoriaId) {
            $q->where('id', $categoriaId);
        });
    }

    $stocks = $query->get();

    if ($stocks->isEmpty()) {
        return redirect()->back()->with('error', 'No se encontraron productos con los filtros aplicados.');
    }

    // Obtener nombre de la sucursal
    $sucursal = \App\Models\Sucursal::find($sucursalId);

    // Crear los ítems para el PDF
    $items = $stocks->map(function ($stock) {
        return ExtendedInvoiceItem::make($stock->producto->nombre)
            ->description("Código: {$stock->producto->codigo}")
            ->pricePerUnit(2)
            ->quantity($stock->cantidad_actual ?? 0);
    });

    // Crear el objeto Buyer con los filtros usados
    $buyer = new Buyer([
        'name' => $sucursal->nombre,
        'custom_fields' => [
            'Cantidad menor de' => $cantidadMax,
            'Categoria' => $categoriaId ? \App\Models\Categoria::find($categoriaId)->nombre : 'Todas',
            'Marca' => $marcaId ? \App\Models\Marca::find($marcaId)->nombre : 'Todas',
        ],
    ]);

    // Crear el PDF
    $invoice = Invoice::make()
        ->name('Pedido')
        ->buyer($buyer)
        ->template('pedidos')
        ->dateFormat('d/m/Y')
        ->currencySymbol('Q')
        ->currencyCode('GTQ')
        ->addItems($items->toArray());

    return $invoice->stream();
    }
}


