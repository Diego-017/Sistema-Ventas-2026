<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table    = 'proveedores';
    protected $fillable = [
        'nombre','razon_social','contacto','telefono','email','direccion',
        'departamento','municipio','giro',
        'tipo_documento','nit','nrc','dui',
        'aplica_percepcion','aplica_retencion_renta','activo',
    ];
    protected $casts = [
        'aplica_percepcion'       => 'boolean',
        'aplica_retencion_renta'  => 'boolean',
        'activo'                  => 'boolean',
    ];
    public $timestamps  = false;

    public function productos() { return $this->hasMany(Producto::class, 'proveedor_id'); }
    public function compras()   { return $this->hasMany(Compra::class,   'proveedor_id'); }
}
