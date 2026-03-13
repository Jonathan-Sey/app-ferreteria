<?php

namespace App\Imports;

use App\Models\PurchaseInvoiceItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Compra2Import implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PurchaseInvoiceItem([
            'id'           => $row['id'],
            'id_compra'       => $row['id_compra'],
            'numerolinea'    => $row['numerolinea'],
            'cantidad'     => $row['cantidad'],
            'producto_id' => $row['producto_id'],
            'precio_unitario'    => $row['precio_unitario'],
            'precio_parcial'       => $row['precio_parcial'],
            'descuento'       => $row['descuento'],
            'otros_descuentos'       => $row['otros_descuentos'],
            'total'       => $row['total'],
            'impuesto'       => $row['impuesto'],
            'created_at'   => $row['created_at'],
            'updated_at'   => $row['updated_at'],
            'deleted_at'   => $row['deleted_at'],
        ]);
    }
}
