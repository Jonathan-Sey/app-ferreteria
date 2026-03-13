<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No hay items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="alert alert-warning text-center">
            <h2>{{ $message }}</h2>
            <hr>
            <p><strong>Sucursal:</strong> {{ $sucursal }}</p>
            <p><strong>Tipo:</strong> {{ $tipo }}</p>
            <p><strong>Período:</strong> {{ $periodo }}</p>
            <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Volver</a>
        </div>
    </div>
</body>
</html>