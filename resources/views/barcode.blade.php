<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codigo de barras</title>
</head>
<body>
    @foreach ($productos as $product)
        <div style="container width: 200px; margin: 20px; padding: 10px; border: 1px solid #000;">
            {!! DNS1D::getBarcodeHTML($product->codigo, 'C128') !!}
            <div>
                CODIGO: {{ $product->codigo }}<br>
               NOMBRE: {{ $product->nombre }}<br>
               FECHA: {{ $product->fecha }}<br>
                ID: {{ $product->id }}<br>

            </div>
        </div>
        
    @endforeach
</body>
</html>