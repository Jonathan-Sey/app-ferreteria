<!DOCTYPE html>
<html lang="es">
<head>
    <title>Corte de Caja</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css" media="screen">
        html {
            font-family: sans-serif;
            line-height: 1.15;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 36pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #000;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .mt-5 {
            margin-top: 3rem;
        }

        .cool-gray {
            color: #6B7280;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            border: none;
            padding: 3px 0;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <table class="table mt-5">
        <tbody>
            <tr>
                <td class="text-center" colspan="2">
                    <h4 class="text-uppercase">
                        <strong>FerreteriaLaPaz</strong>
                    </h4>
                </td>
            </tr>
            <tr>
                <td class="text-center" colspan="2">
                    <h4 class="text-uppercase">
                        <strong>CORTE DE CAJA</strong>
                    </h4>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Información del reporte --}}
    <table class="info-table">
        <tr>
            <td><strong>Sucursal:</strong> {{ $invoice->seller->custom_fields['sucursal'] ?? 'N/A' }}</td>
            <td><strong>Vendedor:</strong> {{ $invoice->seller->custom_fields['vendedor'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Fecha Inicio:</strong> {{ $invoice->seller->custom_fields['fecha_inicio'] ?? 'N/A' }}</td>
            <td><strong>Fecha Fin:</strong> {{ $invoice->seller->custom_fields['fecha_fin'] ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Tabla de ventas --}}
    <table class="table">
        <thead>
            <tr>
                <th>Serie - Factura</th>
                <th>Fecha</th>
                <th>Vendedor</th>
                <th>Nombre del cliente</th>
                <th>Valor</th>
                <th>Tasa y conversión</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            {{-- Items --}}
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->title }}</td> <!-- Número de factura -->
                    <td>{{ $item->units }}</td> <!-- Fecha -->
                    <td>{{ $item->vendedor ?? 'N/A' }}</td> <!-- Nuevo campo para vendedor -->
                    <td>{{ $item->description }}</td> <!-- Nombre del cliente -->
                    <td class="text-right">{{ $invoice->formatCurrency($item->price_per_unit) }}</td> <!-- Valor -->
                    <td>0.00</td> <!-- Tasa y conversión -->
                    <td>N/A</td> <!-- Status -->
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totales --}}
    <table class="table">
        <tbody>
            <tr>
                <td colspan="7"><strong>Total en ventas:</strong> {{ $invoice->formatCurrency($invoice->total_amount) }}</td>
            </tr>
            <tr>
                <td colspan="7"><strong>Efectivo:</strong> Q{{ $invoice->seller->custom_fields['total_efectivo'] ?? '0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"><strong>Crédito:</strong> Q{{ $invoice->seller->custom_fields['total_credito'] ?? '0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"><strong>Tarjeta:</strong> Q{{ $invoice->seller->custom_fields['total_tarjeta'] ?? '0.00' }}</td>
            </tr>
            <tr>
                <td colspan="7"><strong>Transferencia:</strong> Q{{ $invoice->seller->custom_fields['total_transferencia'] ?? '0.00' }}</td>
            </tr>
        </tbody>
    </table>

    <script type="text/php">
        if (isset($pdf) && $PAGE_COUNT > 1) {
            $text = "Página {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width);
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>