<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FelCertificationService
{

    /**
     * Genera el XML para una venta
     * @param Venta $venta
     * @return string
     */
    private function generateXML(Venta $venta)
    {
        $fechaEmision = $venta->created_at; // O ES EL CAMPO fechahora_emision
        $fechaFormateada = Carbon::parse($fechaEmision)
            ->setTimezone('America/Guatemala') // Asegurar la zona horaria correcta
            ->format('Y-m-d\TH:i:s.vP');
        // Asumiendo que tienes relaciones definidas en tu modelo Venta
        $emisor = $venta->emisor;
        $receptor = $venta->cliente;
        $detalles = $venta->items;

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<dte:GTDocumento xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\" 
                xmlns:dte=\"http://www.sat.gob.gt/dte/fel/0.2.0\" 
                xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" 
                Version=\"0.1\" xsi:schemaLocation=\"http://www.sat.gob.gt/dte/fel/0.2.0\">\n";
        $xml .= "    <dte:SAT ClaseDocumento=\"dte\">\n";
        $xml .= "        <dte:DTE ID=\"DatosCertificados\">\n";
        $xml .= "            <dte:DatosEmision ID=\"DatosEmision\">\n";
        $xml .= "                <dte:DatosGenerales CodigoMoneda=\"GTQ\" 
                    FechaHoraEmision=\"{$fechaFormateada}\" Tipo=\"FACT\"></dte:DatosGenerales>\n";

        // Datos del Emisor
        $xml .= $this->generateEmisorXML($emisor);

        // Datos del Receptor
        $xml .= $this->generateReceptorXML($receptor);

        // Frases
        $xml .= "                <dte:Frases>\n";
        $xml .= "                    <dte:Frase CodigoEscenario=\"1\" TipoFrase=\"1\"></dte:Frase>\n";
        $xml .= "                </dte:Frases>\n";

        // Items
        $xml .= "                <dte:Items>\n";

        $sumTotal = 0;
        $totalImpuestosPorTipo = [];
        foreach ($detalles as $index => $detalle) {
            $sumTotal += $detalle->total;

            foreach ($detalle->impuestos as $impuesto) {
                $nombreCorto = $impuesto->impuesto->impuestoTipo->descripcion;
                if (!isset($totalImpuestosPorTipo[$nombreCorto])) {
                    $totalImpuestosPorTipo[$nombreCorto] = [
                        'total' => 0,
                        'codigo' => $impuesto->impuesto->impuestoTipo->codigo
                    ];
                }
                $totalImpuestosPorTipo[$nombreCorto]['total'] += $impuesto->monto_impuesto;
            }

            $xml .= $this->generateItemXML($detalle, $index + 1);
        }

        $xml .= "                </dte:Items>\n";

        // Totales
        $xml .= "                <dte:Totales>\n";
        $xml .= "                    <dte:TotalImpuestos>\n";

        foreach ($totalImpuestosPorTipo as $nombreCorto => $datos) {
            $xml .= "                        <dte:TotalImpuesto NombreCorto=\"{$nombreCorto}\" 
                            TotalMontoImpuesto=\"{$datos['total']}\"></dte:TotalImpuesto>\n";
        }

        $xml .= "                    </dte:TotalImpuestos>\n";
        $xml .= "                    <dte:GranTotal>{$sumTotal}</dte:GranTotal>\n";
        $xml .= "                </dte:Totales>\n";
        $xml .= "            </dte:DatosEmision>\n";
        $xml .= "        </dte:DTE>\n";
        $xml .= "    </dte:SAT>\n";
        $xml .= "</dte:GTDocumento>";

        return $xml;
    }

    /**
     * Genera el XML para el emisor
     * @param $emisor
     * @return string
     */
    private function generateEmisorXML($emisor)
    {
        $xml = "                <dte:Emisor AfiliacionIVA=\"" . ($emisor->afiliacionIva->abreviacion ?? 'GEN') . "\" CodigoEstablecimiento=\"" . ($emisor->cod_establecimiento ?? 1) . "\" 
                NITEmisor=\"" . ($emisor->nit ?? 'CF') . "\" NombreComercial=\"{$emisor->nombre_comercial}\" 
                NombreEmisor=\"{$emisor->nombre}\">\n";
        $xml .= "                    <dte:DireccionEmisor>\n";
        $xml .= "                        <dte:Direccion>" . ($emisor->direccion ?? '') . "</dte:Direccion>\n";
        $xml .= "                        <dte:CodigoPostal>" . ($emisor->codigo_postal ?? '') . "</dte:CodigoPostal>\n";
        $xml .= "                        <dte:Municipio>" . ($emisor->municipio->nombre ?? '') . "</dte:Municipio>\n";
        $xml .= "                        <dte:Departamento>" . ($emisor->municipio->departamento->nombre ?? '') . "</dte:Departamento>\n";
        $xml .= "                        <dte:Pais>" . ($emisor->municipio->departamento->pais->abreviatura ?? 'GT') . "</dte:Pais>\n";
        $xml .= "                    </dte:DireccionEmisor>\n";
        $xml .= "                </dte:Emisor>\n";

        return $xml;
    }

    /**
     * Genera el XML para el receptor
     * @param $receptor
     * @return string
     */
    private function generateReceptorXML($receptor)
    {
        $xml = "                <dte:Receptor IDReceptor=\"" . ($receptor->nit ?? 'CF') . "\"
                NombreReceptor=\"" . ($receptor->nombre ?? 'Consumidor Final') . "\"" . ">\n";
        $xml .= "                    <dte:DireccionReceptor>\n";
        $xml .= "                        <dte:Direccion>" . ($receptor->direccion ?? 'CIUDAD') . "</dte:Direccion>\n";
        $xml .= "                        <dte:CodigoPostal>" . ($receptor->codigo_postal ?? '') . "</dte:CodigoPostal>\n";
        $xml .= "                        <dte:Municipio>" . ($receptor->municipio->nombre ?? '') . "</dte:Municipio>\n";
        $xml .= "                        <dte:Departamento>" . ($receptor->municipio->departamento->nombre ?? '') . "</dte:Departamento>\n";
        $xml .= "                        <dte:Pais>" . ($receptor->municipio->departamento->pais->abreviatura ?? 'GT') . "</dte:Pais>\n";
        $xml .= "                    </dte:DireccionReceptor>\n";
        $xml .= "                </dte:Receptor>\n";

        return $xml;
    }

    /**
     * Genera el XML para un item de la venta
     * @param $detalle
     * @param $lineNumber
     * @return string
     */

    private function generateItemXML($detalle, $lineNumber)
    {

        $xml = "<dte:Item BienOServicio=\"{$detalle->tipo}\" NumeroLinea=\"{$lineNumber}\">\n" .
            "       <dte:Cantidad>{$detalle->cantidad}</dte:Cantidad>\n" .
            "       <dte:UnidadMedida>UND</dte:UnidadMedida>\n" .
            "       <dte:Descripcion>{$detalle->producto->nombre}</dte:Descripcion>\n" .
            "       <dte:PrecioUnitario>{$detalle->precio_unitario}</dte:PrecioUnitario>\n" .
            "       <dte:Precio>{$detalle->total}</dte:Precio>\n" .
            "       <dte:Descuento>{$detalle->descuento}</dte:Descuento>\n" .
            "       <dte:Impuestos>\n";

        foreach ($detalle->impuestos as $key => $impuesto) {
            $xml .= $this->generateImpuestoXML($impuesto);
        }

        $xml .= "   </dte:Impuestos>\n" .
            "      <dte:Total>{$detalle->total}</dte:Total>\n" .
            " </dte:Item>\n";

        return $xml;
    }

    /**
     * Genera el XML para un impuesto de un item de la venta
     * @param $impuesto
     * @return string
     */

    private function generateImpuestoXML($impuesto)
    {
        return "<dte:Impuesto>\n" .
            "        <dte:NombreCorto>{$impuesto->impuesto->impuestoTipo->descripcion}</dte:NombreCorto>\n" .
            "        <dte:CodigoUnidadGravable>{$impuesto->impuesto->codigo}</dte:CodigoUnidadGravable>\n" .
            "        <dte:MontoGravable>{$impuesto->monto_gravable}</dte:MontoGravable>\n" .
            "        <dte:MontoImpuesto>{$impuesto->monto_impuesto}</dte:MontoImpuesto>\n" .
            "   </dte:Impuesto>\n";
    }

    /**
     * Certifica una venta en FEL
     * @param Venta $venta
     * @return mixed
     * @throws \Exception
     */

    public function certifyVenta(Venta $venta)
    {
        $xml = $this->generateXML($venta);

        Log::info('XML a enviar: ' . $xml);

        $response = Http::withHeaders([
            // 'Content-Type' => 'application/xml',
            'LlaveApi' => config('services.fel.llave_api'),
            'LlaveFirma' => config('services.fel.llave_firma'),
            'UsuarioApi' => config('services.fel.usuario_api'),
            'UsuarioFirma' => config('services.fel.usuario_firma'),
            'identificador' => $venta->id
        ])->post(config('services.fel.url'), $xml);

        Log::info('RESPONSE: ' . $response);

        if ($response->successful()) {
            $responseData = $response->json();

            // Verificar si la certificación fue exitosa
            if (!$responseData['resultado']) {
                Log::error('Error en certificación FEL', [
                    'venta_id' => $venta->id,
                    'errores' => $responseData['descripcion_errores'] ?? [],
                    'descripcion' => $responseData['descripcion'] ?? 'Sin descripción'
                ]);

                throw new \Exception(
                    'Error en certificación FEL: ' .
                        ($responseData['descripcion'] ?? 'Error desconocido')
                );
            }

            // Si fue exitosa, actualizar la venta con los datos de certificación
            $venta->update([
                'certificada' => 1,
                'serie' => $responseData['serie'] ?? null,
                'no_autorizacion' => $responseData['numero'] ?? null,
                'codigo_autorizacion' => $responseData['uuid'] ?? null,
                'fechahora_certificacion' => $responseData['fecha'] ?? null,
                // 'fechahora_emision' => $responseData['fecha'] ?? null, //verificar si se actualiza 
                // 'fel_response' => json_encode($responseData)
            ]);

            Log::info('Venta certificada correctamente', [
                'venta_id' => $venta->id,
                'uuid' => $responseData['uuid'] ?? null
            ]);

            return $responseData;
        }

        throw new \Exception('Error al certificar la venta: ' . $response->body());
    }
}
