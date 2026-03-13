<?php

namespace App\Filament\Resources\SucursalResource\Pages;
use App\Helpers\SucursalHelper;
use App\Filament\Resources\SucursalResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CambiarSucursal extends Page
{
    protected static string $resource = SucursalResource::class;
    protected static ?string $navigationIcon = 'custom-swap';
    protected static string $view = 'filament.resources.sucursal-resource.pages.cambiar-sucursal';

    // protected static ?string $navigationIcon = 'heroicon-o-switch-horizontal';
    // protected static string $view = 'filament.pages.cambiar-sucursal';

    //protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 20;

    public $sucursal_id;
    public $sucursales;
    public $sucursalActual;

    public function mount()
    {
        // Cargar las sucursales asignadas al usuario
        $this->sucursales = DB::table('sucursales_usuario')
            ->where('id_usuario', auth()->id())
            ->join('sucursales', 'sucursales.id', '=', 'sucursales_usuario.id_sucursal')
            ->pluck('sucursales.nombre', 'sucursales.id')
            ->toArray();

        // Obtener la sucursal actual desde la sesión
        $this->sucursalActual = session('sucursal_actual');

        // Si hay una sucursal actual, establecerla como seleccionada
        if ($this->sucursalActual) {
            $this->sucursal_id = $this->sucursalActual->id;
        }
    }

    public function cambiarSucursal(): void
    {
        // Verificar que se haya seleccionado una sucursal
        if (!$this->sucursal_id) {
            $this->addError('sucursal_id', 'Por favor, selecciona una sucursal.');
            return;
        }

        // Corregir la condición del join
        $sucursal = DB::table('sucursales_usuario')
            ->where('id_usuario', auth()->id())
            ->where('id_sucursal', $this->sucursal_id)
            ->join('sucursales', 'sucursales.id', '=', 'sucursales_usuario.id_sucursal') // Corregido aquí
            ->select('sucursales.*')
            ->first();

        if ($sucursal) {
            // Actualizar la sucursal en la sesión
            Session::put('sucursal_actual', $sucursal);
            session()->flash('message', 'Sucursal cambiada correctamente.');
            $this->sucursalActual = $sucursal;

            Notification::make()
            ->title('Cambio de Sucursal realizado.')
            ->icon('heroicon-o-face-smile')
            ->success()
            ->send();
        } else {
            session()->flash('error', 'No tienes acceso a esta sucursal.');
        }
    }
}
