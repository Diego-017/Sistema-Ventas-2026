<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table    = 'ventas';
    protected $fillable = [
        'codigo','cliente_id','usuario_id',
        'subtotal','descuento','total',
        'metodo_pago','estado','notas',
    ];
    const UPDATED_AT = null;

    public function cliente() { return $this->belongsTo(Cliente::class, 'cliente_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function items()   { return $this->hasMany(VentaItem::class, 'venta_id'); }
}
