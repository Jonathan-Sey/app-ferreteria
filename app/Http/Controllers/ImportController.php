<?php

namespace App\Http\Controllers;
use  Maatwebsite\Excel\Facades\Excel;
use App\Imports\SucurImport;
use App\Models\Sucursal;

use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function import()
    {
        // Importar el archivo Excel
        Excel::import(new SucurImport, request()->file('file'));

        // Redirigir a la página anterior con un mensaje de éxito
        return redirect()->back()->with('success', 'Archivo importado correctamente.');
    }

    public function export()
    {
        return Excel::download(new SucurExport, 'sucursales.xlsx');
    }
}
