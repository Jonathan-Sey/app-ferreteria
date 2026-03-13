<?php

namespace App\Classes;

use LaravelDaily\Invoices\Classes\InvoiceItem as BaseInvoiceItem;

class ExtendedInvoiceItem extends BaseInvoiceItem
{
    /**
     * @var string
     */
    public $customFields;

    /**
     * @var string
     */
    public $marca;

    /**
     * @var int
     */
    public $totalProducto;

    /**
     * @var float
     */
    public $totalCompras;
    /**
     * @var float
     */
    public $totalVentas;
    /**
     * @var float
     */
    public $totalMayoreos;

    /**
     * @var string
     */
    public $ProductoKarnex;

    /**
     * @var string
     */
    public $SucursalKarnex;


    /**
     * @var string
     */
    public $fecha;

    /**
     * @var int
     */
    public $ingreso;

    /**
     * @var int
     */
    public $egreso;

    /**
     * @var int
     */
    public $stock;

    /**
     * @var int
     */
    public $stockIndividual;

    /**
     * @var string
     */
    public $tipo;

    /**
     * @var string
     */
    public $concepto;

    /**
     * @var string
     */
    public $proveedor;

    /**
     * @var string
     */
    public $nofactura;

    /**
     * @var string
     */
    public $cliente;

    /**
     * @var string
     */
    public $producto;

    /**
     * @var string
     */
    public $dia;

    /**
     * @var string
     */

    public $credito;

    /**
     * @var string
     */
    public $tarjeta;

    /**
     * @var float
     */
    public $precioventa;

    /**
     * @var float
     */
    public $preciocompra;

    /**
     * @var float
     */
    public $ganancia;

    /**
     * @var float
     */
    public $gananciabruta;

    /**
     * @var float
     */
    public $porcentaje;

    /**
     * @var string
     */
    public $documento;

    /**
     * @var float
     */
    public $servicio;

    /**
     * @var string
     */
    public $precio;

    /**
     * @var string
     */
    public $servicios;

    /**
     * @var string
     */
    public $iva;

     /**
     * @var string
     */
    public $total;
    

/**
 * @var array
 */
public $totalesMetodosPago;


/**
 * @var float
 */
public $totalEfectivo;

/**
 * @var float
 */
public $totalTarjeta;

/**
 * @var float
 */
public $totalCredito;

/**
 * @var float
 */
public $totalTransferencia;

/**
 * @var string
 */
public $vendedor;

/**
 * @var string
 */
public $descripcion;


    // Sobrescribe el método make()
    public static function make($title)
    {
        return (new static())->title($title); // Crea instancia de ExtendedInvoiceItem
    }



    /**
     * @param string $customFields
     * @return $this
     */
    public function addCustomField(?string $customFields)
    {
        $this->customFields = $customFields;
        return $this;   
    }


    /**
     * @param string $marca
     * @return $this
     */
    public function Marca(?string $marca)
    {
        $this->marca = $marca;
        return $this;   
    }

    /**
     * @param int $totalProducto
     * @return $this
     */
    public function TotalPro(?int $totalProducto)
    {
        $this->totalpro = $totalProducto;
        return $this;   
    }


    /**
     * @param float $totalCompras
     * @return $this
     */
    public function totalCompras(?float $totalCompras)
    {
        $this->totalcompras = $totalCompras;
        return $this;   
    }
    /**
     * @param float $totalVentas
     * @return $this
     */
    public function totalVentas(?float $totalVentas)
    {
        $this->totalventas = $totalVentas;
        return $this;   
    }
    /**
     * @param float $totalMayoreos
     * @return $this
     */
    public function totalMayoreos(?float $totalMayoreos)
    {
        $this->totalmayoreos = $totalMayoreos;
        return $this;   
    }

    /**
     * @param string $ProductoKarnex
     * @return $this
     */
    public function productoKarnex(?string $ProductoKarnex)
    {
        $this->productoka = $ProductoKarnex;
        return $this;   
    }

    /**
     * @param string $SucursalKarnex
     * @return $this
     */
    public function Sucursal(?string $SucursalKarnex)
    {
        $this->sucursal = $SucursalKarnex;
        return $this;   
    }

    // Kardex--------------------------------------------

    /**
     * @param string $fecha
     * @return $this
     */
    public function Fecha(?string $fecha)
    {
        $this->fecha = $fecha;
        return $this;   
    }

    /**
     * @param int $ingreso
     * @return $this
     */
    public function Ingreso(?int $ingreso)
    {
        $this->ingreso = $ingreso;
        return $this;   
    }

    /**
     * @param int $egreso
     * @return $this
     */
    public function Egreso(?int $egreso)
    {
        $this->egreso = $egreso;
        return $this;   
    }

    /**
     * @param int $stock
     * @return $this
     */
    public function Stock(?int $stock)
    {
        $this->stock = $stock;
        return $this;   
    }

    /**
     * @param int $stockIndividual
     * @return $this
     */
    public function StockIndividual(?int $stockIndividual)
    {
        $this->stockind = $stockIndividual;
        return $this;   
    }


    /**
     * @param string $tipo
     * @return $this
     */
    public function Tipo(?string $tipo)
    {
        $this->tipo = $tipo;
        return $this;   
    }


    /**
     * @param string $concepto
     * @return $this
     */
    public function Concepto(?string $concepto)
    {
        $this->concepto = $concepto;
        return $this;   
    }


    /**
     * @param string $proveedor
     * @return $this
     */
    public function Proveedor(?string $proveedor)
    {
        $this->proveedor = $proveedor;
        return $this;   
    }


    /**
     * @param string $nofactura
     * @return $this
     */
    public function NoFactura(?string $nofactura)
    {
        $this->nofactura = $nofactura;
        return $this;   
    }


    /**
     * @param string $cliente
     * @return $this
     */
    public function Cliente(?string $cliente)
    {
        $this->cliente = $cliente;
        return $this;   
    }


    /**
     * @param string $producto
     * @return $this
     */
    public function Producto(?string $producto)
    {
        $this->producto = $producto;
        return $this;   
    }


    /**
     * @param string $dia
     * @return $this
     */
    public function Dia(?string $dia)
    {
        $this->dia = $dia;
        return $this;   
    }


    /**
     * @param string $credito
     * @return $this
     */
    public function Credito(?string $credito)
    {
        $this->credito = $credito;
        return $this;   
    }


    /**
     * @param string $tarjeta
     * @return $this
     */
    public function Tarjeta(?string $tarjeta)
    {
        $this->tarjeta = $tarjeta;
        return $this;   
    }


    /**
     * @param float $precioventa
     * @return $this
     */
    public function precioVenta(?float $precioventa)
    {
        $this->precioventa = $precioventa;
        return $this;   
    }

    /**
     * @param float $preciocompra
     * @return $this
     */
    public function precioCompra(?float $preciocompra)
    {
        $this->preciocompra = $preciocompra;
        return $this;   
    }

    /**
     * @param float $ganancia
     * @return $this
     */
    public function Ganancia(?float $ganancia)
    {
        $this->ganancia = $ganancia;
        return $this;   
    }

    /**
     * @param float $gananciabruta
     * @return $this
     */
    public function gananciaBruta(?float $gananciabruta)
    {
        $this->gananciabruta = $gananciabruta;
        return $this;   
    }

    /**
     * @param float $porcentaje
     * @return $this
     */
    public function Porcentaje(?float $porcentaje)
    {
        $this->porcentaje = $porcentaje;
        return $this;   
    }

    /**
     * @param string $documento
     * @return $this
     */
    public function Documento(?string $documento)
    {
        $this->documento = $documento;
        return $this;   
    }


    /**
     * @param float $servicio
     * @return $this
     */
    public function Servicio(?float $servicio)
    {
        $this->servicio = $servicio;
        return $this;   
    }

// Métodos nuevos requeridos para el libro de ventas



/**
 * @param string $precio
 * @return $this
 */
public function Precio(?string $precio)
{
    $this->precio = $precio;
    return $this;
}

/**
 * @param string $servicios
 * @return $this
 */
public function Servicios(?string $servicios)
{
    $this->servicios = $servicios;
    return $this;
}

/**
 * @param string $iva
 * @return $this
 */
public function Iva(?string $iva)
{
    $this->iva = $iva;
    return $this;
}

    /**
     * @param string $total
     * @return $this
     */
    public function Total(?string $total)
    {
        $this->total = $total;
        return $this;
    }

    /**
 * @param array $totales
 * @return $this
 */
public function TotalesMetodosPago(array $totales)
{
    $this->totalesMetodosPago = $totales;
    return $this;
}


/**
 * @param float $total
 * @return $this
 */
public function TotalEfectivo(float $total)
{
    $this->totalEfectivo = $total;
    return $this;
}



/**
 * @param float $total
 * @return $this
 */
public function TotalTarjeta(float $total)
{
    $this->totalTarjeta = $total;
    return $this;
}



/**
 * @param float $total
 * @return $this
 */
public function TotalCredito(float $total)
{
    $this->totalCredito = $total;
    return $this;
}


/**
 * @param float $total
 * @return $this
 */
public function TotalTransferencia(float $total)
{
    $this->totalTransferencia = $total;
    return $this;
}

/**
 * @param string $vendedor
 * @return $this
 */
public function Vendedor(string $vendedor)
{
    $this->vendedor = $vendedor;
    return $this;
}

    /**
     * @param string $descripcion
     * @return $this
     */
    public function Descripcion(string $descripcion)
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    
}
