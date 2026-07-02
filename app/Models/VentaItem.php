<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VentaItem extends Model
{
    protected $table    = 'venta_items';
    protected $fillable = [
        'venta_id','producto_id','nombre_producto',
        'cantidad','precio_unitario','descuento','subtotal',
    ];
    public $timestamps = false;
    public function producto() { return $this->belongsTo(Producto::class, 'producto_id'); }
}
