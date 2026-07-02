<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CotizacionItem extends Model
{
    protected $table    = 'cotizacion_items';
    protected $fillable = ['cotizacion_id','producto_id','nombre_producto','cantidad','precio_unitario','subtotal'];
    public $timestamps  = false;

    public function producto()   { return $this->belongsTo(Producto::class,   'producto_id'); }
    public function cotizacion() { return $this->belongsTo(Cotizacion::class, 'cotizacion_id'); }
}
