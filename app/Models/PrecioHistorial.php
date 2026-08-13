<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PrecioHistorial extends Model
{
    protected $table    = 'precios_historial';
    protected $fillable = ['producto_id','usuario_id','precio_compra_anterior','precio_compra_nuevo','precio_venta_anterior','precio_venta_nuevo','motivo'];
    const UPDATED_AT    = null;

    public function producto() { return $this->belongsTo(Producto::class, 'producto_id'); }
    public function usuario()  { return $this->belongsTo(Usuario::class,  'usuario_id'); }
}
