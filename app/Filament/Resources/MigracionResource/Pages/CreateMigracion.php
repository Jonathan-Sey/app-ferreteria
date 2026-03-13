<?php

namespace App\Filament\Resources\MigracionResource\Pages;

use App\Filament\Resources\MigracionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SucurImport;
use App\Imports\ClienteImport;
use App\Imports\Ajustep1Import;
use App\Imports\Ajustep2Import;
use App\Imports\ProductoImport;
use App\Imports\ProveedorImport;
use App\Imports\VendedorImport;
use App\Imports\Traslado1Import;
use App\Imports\Traslado2Import;
use App\Imports\Compra1Import;
use App\Imports\Compra2Import;
use App\Imports\Fac1Import;
use App\Imports\Fac2Import;
use App\Imports\StockImport;
use App\Models\Migracion;
use App\Imports\CategoriaImport;
use App\Imports\MarcaImport;

class CreateMigracion extends CreateRecord
{
    protected static string $resource = MigracionResource::class;

    // Sobrescribimos el método para procesar la importación sin crear un registro real.
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (isset($data['attachment'])) {
            // Usa la ruta real del archivo en el disco 'public' (con extensión)
            $filePath = Storage::disk('public')->path($data['attachment']);

            if (! file_exists($filePath)) {
                throw new \Exception("Archivo no encontrado: {$filePath}");
            }

            // Seleccionar el importador adecuado según la tabla seleccionada
            $importClass = $this->getImportClass($data['tabla']);

            if ($importClass) {
                Excel::import(new $importClass, $filePath);
                // Si quisieras forzar el tipo según la extensión, descomenta:
                // $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                // $type = match ($ext) {
                //     'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
                //     'xls'  => \Maatwebsite\Excel\Excel::XLS,
                //     'csv'  => \Maatwebsite\Excel\Excel::CSV,
                //     default => null,
                // };
                // Excel::import(new $importClass, $filePath, null, $type);
            } else {
                throw new \Exception("No se encontró un importador para la tabla seleccionada");
            }
        }

        // Devuelve una instancia nueva para que la lógica de CreateRecord complete sin errores.
        return new Migracion;
    }

    protected function getImportClass(string $tableName): ?string
    {
        // Mapeo de nombres de tabla a clases de importación
        $importers = [
            'sucursales' => SucurImport::class,
            'entidades' => ClienteImport::class,
            'movimientos_inventario' => Ajustep1Import::class,
            'movimientos_inventario_detalle' => Ajustep2Import::class,
            'productos' => ProductoImport::class,
            'entidades' => ProveedorImport::class,
            'users' => VendedorImport::class,
            'movimientos_inventario' => Traslado1Import::class,
            'movimientos_inventario_detalle' => Traslado2Import::class,
            'compras' => Compra1Import::class,
            'compra_items' => Compra2Import::class,
            'ventas' => Fac1Import::class,
            'ventas_item' => Fac2Import::class,
            'inventario_stock' => StockImport::class,
            'categorias' => CategoriaImport::class,
            'marcas' => MarcaImport::class,
        ];

        return $importers[$tableName] ?? null;
    }

    // Redirige a la lista de migraciones para evitar el error de ruta al editar
    protected function getRedirectUrl(): string
    {
        return route('filament.admin.resources.migracions.index');
    }
}