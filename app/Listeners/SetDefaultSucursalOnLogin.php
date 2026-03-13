<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SetDefaultSucursalOnLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Buscar la sucursal predeterminada
        $sucursalPredeterminada = DB::table('sucursales_usuario')
            ->where('id_usuario', $user->id)
            ->where('default', 1)
            ->join('sucursales', 'sucursales.id', '=', 'sucursales_usuario.id_sucursal')
            ->select('sucursales.*')
            ->first();

        // Guardar la sucursal en la sesión
        if ($sucursalPredeterminada) {
            Session::put('sucursal_actual', $sucursalPredeterminada);
        }
    }
}
