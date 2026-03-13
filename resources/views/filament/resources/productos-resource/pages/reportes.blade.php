<div class="container">
    <h1>Lista de Productos</h1>

    <div class="flex space-x-2">
        <x-filament::button color="primary" tag="a" href="#" onclick="generarReporte('pdf')">
            Descargar PDF
        </x-filament::button>

        <x-filament::button color="danger" onclick="generarTabla()">
            Vista Previa
        </x-filament::button>

        <x-filament::button color="success" tag="a" href="#" onclick="generarReporte('xlsx')">
            Descargar Excel
        </x-filament::button>
    </div>

    <!-- Tabla para mostrar los productos -->
    <table id="productosTable" border="1" class="table mt-4" style="width:100%; display:none;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio Venta</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    function generarTabla() {
        document.getElementById("productosTable").style.display = "table";

        fetch("{{ route('productos.data') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error("Error en la respuesta del servidor");
                }
                return response.json();
            })
            .then(data => {
                let tbody = document.querySelector("#productosTable tbody");
                tbody.innerHTML = "";

                data.forEach(producto => {
                    let row = `
                        <tr>
                            <td>${producto.id}</td>
                            <td>${producto.codigo}</td>
                            <td>${producto.nombre}</td>
                            <td>${producto.descripcion}</td>
                            <td>${producto.precio_venta}</td>
                        </tr>`;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => console.error("Error al obtener los datos:", error));
    }

    function generarReporte(tipo) {
        // Crear un formulario dinámicamente y enviarlo a la ruta adecuada de Laravel
        var condi = tipo == "pdf" ? "pdf" : "xlsx";
        var form = document.createElement("form");
        form.method = "POST";
        form.action = "{{ route('productos.exportar') }}";  // Usa la ruta de Laravel

        // Agregar el campo 'condi' al formulario
        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "condi";
        input.value = condi;
        form.appendChild(input);

        // Agregar un token CSRF para protección en Laravel
        var csrfToken = document.createElement("input");
        csrfToken.type = "hidden";
        csrfToken.name = "_token";
        csrfToken.value = "{{ csrf_token() }}";
        form.appendChild(csrfToken);

        // Enviar el formulario
        document.body.appendChild(form);
        form.submit();
    }
</script>
