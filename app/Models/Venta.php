<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table    = 'ventas';
    protected $fillable = [
        'codigo','cliente_id','usuario_id','caja_id',
        'subtotal','descuento','impuesto','venta_exenta','percepcion','retencion','total',
        'metodo_pago','tipo_venta','estado','notas',
    ];
    const UPDATED_AT = null;

    public function cliente() { return $this->belongsTo(Cliente::class, 'cliente_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function caja()    { return $this->belongsTo(Caja::class,    'caja_id'); }
    public function items()   { return $this->hasMany(VentaItem::class, 'venta_id'); }
}
