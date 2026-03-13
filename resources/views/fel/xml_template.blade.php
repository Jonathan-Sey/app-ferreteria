
{{-- 
    Este archivo es un template de Blade que genera el XML de un documento FEL.
    Se debe incluir en el archivo que genera el XML final, pasando las variables
    necesarias para completar la información del documento.
--}}
<?php
Log::info('Generando XML para FEL', [
    'emisor' => $emisor,
    'receptor' => $receptor,
    'detalles' => $detalles,
]);
?>
<?xml verssion="1.0" encoding="UTF-8"?>
<dte:GTDocumento xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0">
    <dte:SAT ClaseDocumento="dte">
        <dte:DatosEmision ID="DatosEmision">
            <dte:Emisor NITEmisor="{{ $emisor->nit }}" NombreEmisor="{{ $emisor->nombre_comercial }}" DireccionEmisor="{{ $emisor->direccion }}" />
            
            <dte:Receptor NITReceptor="{{ $receptor->nit }}" NombreReceptor="{{ $receptor->nombre }}" />

            <dte:Frases>
                <dte:Frase TipoFrase="1" CodigoEscenario="1" />
            </dte:Frases>

            <dte:Items>
                @foreach ($detalles as $detalle)
                    @php
                        $subtotal = $detalle->cantidad * $detalle->precio;
                        $totalImpuestos = collect($detalle->impuestos)->sum('monto'); // Sumar todos los impuestos del ítem
                        $total = $subtotal + $totalImpuestos;
                    @endphp
                    <dte:Item BienOServicio="B" NumeroLinea="{{ $loop->iteration }}">
                        <dte:Cantidad>{{ $detalle->cantidad }}</dte:Cantidad>
                        <dte:UnidadMedida>UND</dte:UnidadMedida>
                        <dte:Descripcion>{{ htmlspecialchars($detalle->producto->nombre, ENT_XML1) }}</dte:Descripcion>
                        <dte:PrecioUnitario>{{ number_format($detalle->precio, 2, '.', '') }}</dte:PrecioUnitario>
                        <dte:Precio>{{ number_format($subtotal, 2, '.', '') }}</dte:Precio>

                        <dte:Impuestos>
                            @foreach ($detalle->impuestos as $impuesto)
                                <dte:Impuesto>
                                    <dte:NombreCorto>{{ $impuesto->tipo }}</dte:NombreCorto>
                                    <dte:CodigoUnidadGravable>1</dte:CodigoUnidadGravable>
                                    <dte:MontoGravable>{{ number_format($subtotal, 2, '.', '') }}</dte:MontoGravable>
                                    <dte:MontoImpuesto>{{ number_format($impuesto->monto, 2, '.', '') }}</dte:MontoImpuesto>
                                </dte:Impuesto>
                            @endforeach
                        </dte:Impuestos>

                        <dte:Total>{{ number_format($total, 2, '.', '') }}</dte:Total>
                    </dte:Item>
                @endforeach
            </dte:Items>

            <dte:Totales>
                @php
                    $totalBruto = $detalles->sum(fn($d) => $d->cantidad * $d->precio);
                    $totalImpuestos = $detalles->sum(fn($d) => collect($d->impuestos)->sum('monto'));
                    $totalNeto = $totalBruto + $totalImpuestos;
                @endphp
                <dte:GranTotal>{{ number_format($totalNeto, 2, '.', '') }}</dte:GranTotal>
            </dte:Totales>
        </dte:DatosEmision>
    </dte:SAT>
</dte:GTDocumento>
