<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CompraItem extends Model
{
    protected $table    = 'compra_items';
    protected $fillable = ['compra_id','producto_id','nombre_producto','cantidad','precio_unitario','subtotal'];
    public $timestamps  = false;
    public function producto() { return $this->belongsTo(Producto::class, 'producto_id'); }
}
