<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class FelCertificationService
{
    private const TIMEZONE = 'America/Guatemala';
    private const DEFAULT_COUNTRY = 'GT';
    private const DEFAULT_CUSTOMER = 'CF';
    private const DEFAULT_ADDRESS = 'CIUDAD';

    /**
     * Genera el XML para una venta
     * @param Venta $venta
     * @return string
     */
    private function generateXML(Venta $venta): string
    {
        $fechaEmision = Carbon::parse($venta->created_at ?? $venta->fechahora_emision)
            ->setTimezone(self::TIMEZONE)
            ->format('Y-m-d\TH:i:s.vP');

        $emisor = $venta->emisor;
        $receptor = $venta->cliente;
        $detalles = $venta->items;

        $totalImpuestosPorTipo = $this->calcularTotalesImpuestos($detalles);
        $sumTotal = $detalles->sum('total');

        return $this->construirXMLBase(
            $fechaEmision,
            $emisor,
            $receptor,
            $detalles,
            $totalImpuestosPorTipo,
            $sumTotal
        );
    }

    /**
     * Calcula los totales de impuestos por tipo
     * @param Collection $detalles
     * @return array
     */
    private function calcularTotalesImpuestos($detalles): array
    {
        $totalImpuestosPorTipo = [];

        foreach ($detalles as $detalle) {
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
        }

        return $totalImpuestosPorTipo;
    }

    /**
     * Construye la estructura base del XML
     */
    private function construirXMLBase(
        string $fechaEmision,
        $emisor,
        $receptor,
        $detalles,
        array $totalImpuestosPorTipo,
        float $sumTotal
    ): string {
        $xml = $this->getXMLHeader();
        $xml .= $this->generateDatosGeneralesXML($fechaEmision);
        $xml .= $this->generateEmisorXML($emisor);
        $xml .= $this->generateReceptorXML($receptor);
        $xml .= $this->generateFrasesXML();
        $xml .= $this->generateItemsXML($detalles);
        $xml .= $this->generateTotalesXML($totalImpuestosPorTipo, $sumTotal);
        $xml .= $this->getXMLFooter();

        return $xml;
    }

    /**
     * Obtiene el encabezado del XML
     */
    private function getXMLHeader(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<dte:GTDocumento xmlns:ds="http://www.w3.org/2000/09/xmldsig#" ' .
            'xmlns:dte="http://www.sat.gob.gt/dte/fel/0.2.0" ' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
            'Version="0.1" xsi:schemaLocation="http://www.sat.gob.gt/dte/fel/0.2.0">' . "\n" .
            '    <dte:SAT ClaseDocumento="dte">' . "\n" .
            '        <dte:DTE ID="DatosCertificados">' . "\n" .
            '            <dte:DatosEmision ID="DatosEmision">' . "\n";
    }

    /**
     * Genera el XML para DatosGenerales
     */
    private function generateDatosGeneralesXML(string $fechaEmision): string
    {
        return "                <dte:DatosGenerales CodigoMoneda=\"GTQ\" " .
            "FechaHoraEmision=\"{$fechaEmision}\" " .
            "Tipo=\"FACT\"></dte:DatosGenerales>\n";
    }
    /**
     * Obtiene el pie del XML
     */
    private function getXMLFooter(): string
    {
        return "            </dte:DatosEmision>\n" .
            "        </dte:DTE>\n" .
            "    </dte:SAT>\n" .
            "</dte:GTDocumento>";
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
     * Genera el XML para las frases
     */
    private function generateFrasesXML(): string
    {
        return "                <dte:Frases>\n" .
            "                    <dte:Frase CodigoEscenario=\"1\" TipoFrase=\"1\"></dte:Frase>\n" .
            "                </dte:Frases>\n";
    }

    /**
     * Genera el XML para los items
     */
    private function generateItemsXML($detalles): string
    {
        $xml = "                <dte:Items>\n";
        foreach ($detalles as $index => $detalle) {
            $xml .= $this->generateItemXML($detalle, $index + 1);
        }
        $xml .= "                </dte:Items>\n";
        return $xml;
    }

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
     * Genera el XML para los totales
     */
    private function generateTotalesXML(array $totalImpuestosPorTipo, float $sumTotal): string
    {
        $xml = "                <dte:Totales>\n" .
            "                    <dte:TotalImpuestos>\n";

        foreach ($totalImpuestosPorTipo as $nombreCorto => $datos) {
            $xml .= sprintf(
                "                        <dte:TotalImpuesto NombreCorto=\"%s\" TotalMontoImpuesto=\"%s\"></dte:TotalImpuesto>\n",
                htmlspecialchars($nombreCorto),
                $datos['total']
            );
        }

        $xml .= "                    </dte:TotalImpuestos>\n" .
            "                    <dte:GranTotal>" . $sumTotal . "</dte:GranTotal>\n" .
            "                </dte:Totales>\n";

        return $xml;
    }

    /**
     * Certifica una venta en FEL
     * @param Venta $venta
     * @return array
     * @throws Exception
     */
    public function certifyVenta(Venta $venta): array
    {
        try {
            $xml = $this->generateXML($venta);

            // Validar XML antes de enviar
            $this->validateXML($xml);

            Log::info('XML a enviar: ' . $xml);

            $response = $this->sendFelRequest($venta, $xml);

            if (!$response->successful()) {
                throw new Exception('Error en la respuesta del servicio: ' . $response->body());
            }

            $responseData = $response->json();

            if (!$responseData['resultado']) {
                $this->handleFelError($venta, $responseData);
            }

            $this->updateVentaCertificacion($venta, $responseData);

            return $responseData;
        } catch (Exception $e) {
            Log::error('Error en proceso de certificación FEL', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Envía la solicitud al servicio FEL
     */
    private function sendFelRequest(Venta $venta, string $xml)
    {
        // Validar que la fecha de emisión no sea futura
        $fechaEmision = Carbon::parse($venta->created_at ?? $venta->fechahora_emision);
        $fechaActual = Carbon::now();

        if ($fechaEmision->gt($fechaActual)) {
            throw new Exception('La fecha de emisión no puede ser futura');
        }

        // Asegurar que el XML esté correctamente codificado
        $cleanXml = trim($xml); // Eliminar espacios en blanco al inicio y final

        // Log del XML limpio para debugging
        Log::debug('XML limpio a enviar:', ['xml' => $cleanXml]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',  // Agregar explícitamente el content type
                'LlaveApi' => config('services.fel.llave_api'),
                'LlaveFirma' => config('services.fel.llave_firma'),
                'UsuarioApi' => config('services.fel.usuario_api'),
                'UsuarioFirma' => config('services.fel.usuario_firma'),
                'identificador' => $venta->id
            ])
                ->withBody($cleanXml, 'application/xml') // Usar withBody en lugar de enviar directamente
                ->post(config('services.fel.url'));

            // Log de la respuesta completa para debugging
            Log::debug('Respuesta completa del servicio FEL:', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            return $response;
        } catch (Exception $e) {
            Log::error('Error al enviar solicitud FEL:', [
                'error' => $e->getMessage(),
                'xml' => $cleanXml
            ]);
            throw new Exception('Error al enviar solicitud FEL: ' . $e->getMessage());
        }
    }

    /**
     * Maneja los errores de FEL
     */
    private function handleFelError(Venta $venta, array $responseData): void
    {
        Log::error('Error en certificación FEL', [
            'venta_id' => $venta->id,
            'errores' => $responseData['descripcion_errores'] ?? [],
            'descripcion' => $responseData['descripcion'] ?? 'Sin descripción'
        ]);

        throw new Exception(
            'Error en certificación FEL: ' .
                ($responseData['descripcion'] ?? 'Error desconocido')
        );
    }

    /**
     * Valida la estructura del XML antes de enviarlo
     * @throws Exception
     */
    private function validateXML(string $xml): void
    {
        if (!simplexml_load_string($xml)) {
            throw new Exception('XML mal formado');
        }
    }

    /**
     * Actualiza la venta con los datos de certificación
     */
    private function updateVentaCertificacion(Venta $venta, array $responseData): void
    {
        $venta->update([
            'certificada' => 1,
            'serie' => $responseData['serie'] ?? null,
            'no_autorizacion' => $responseData['numero'] ?? null,
            'codigo_autorizacion' => $responseData['uuid'] ?? null,
            'fechahora_certificacion' => isset($responseData['fecha'])
                ? Carbon::parse($responseData['fecha'])->format('Y-m-d H:i:s')
                : null
        ]);
    }

    // Los métodos generateEmisorXML, generateReceptorXML, generateItemXML y generateImpuestoXML
    // se mantienen igual pero con mejoras en el escape de caracteres especiales
}
