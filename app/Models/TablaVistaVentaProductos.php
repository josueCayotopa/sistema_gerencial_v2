<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TablaVistaVentaProductos extends Model
{
    use HasFactory;

    protected $table = 'TABLA_VISTA_VENTA_PRODUCTOS';
    protected $primaryKey = 'OPERACION';
    public $incrementing = false;
    public $timestamps = false;
    
    // Solo los campos realmente necesarios
    protected $fillable = [
        'PERIODO',
        'MES',
        'RAZON_SOCIAL',
        'GRUPO',
        'SUBGRUPO',
        'COD_FAMILIAP',
        'DES_FAMILIA',
        'COD_FAMILIA',
        'DES_SUBFAMILIA',
        'CODIGO',
        'PRODUCTO',
        'PRECIOPROM',
        'CANTIDAD',
        'VEN_CIGV',
        'DOCUMENTO',
        'SUCURSAL',
        'FEC_EMISION',
        'FEC_ACTUALIZA',
        'OPERACION',
        'NUM_PARTE_PROV',
        'CLIENTE',
        'DOC_CLIENTE',
        'CLIENTE_EMAIL',
        'CLIENTE_TELEFONO',
        'SUCURSAL',
        'FEC_EMISION',
        'VEN_CIGV',
        'CANTIDAD',
        'PRODUCTO',
        'TIP_ESTADO',
    ];
    
    protected $dates = ['FEC_EMISION'];
    
   
}