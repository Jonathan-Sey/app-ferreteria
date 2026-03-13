<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

class SucursalHelper
{
    public static function getSucursalId()
    {
        return Session::get('sucursal_actual')->id ?? null;
    }

    public static function getSucursal()
    {
        return Session::get('sucursal_actual') ?? null;
    }

    public static function setSucursal($sucursal)
    {
        Session::put('sucursal_actual', $sucursal);
    }
}