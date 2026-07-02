<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table    = 'cotizaciones';
    protected $fillable = ['codigo','cliente_id','usuario_id','subtotal','descuento','total','estado','valida_hasta','notas'];
    const UPDATED_AT    = null;
    protected $casts    = ['valida_hasta' => 'date'];

    public function cliente() { return $this->belongsTo(Cliente::class, 'cliente_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function items()   { return $this->hasMany(CotizacionItem::class, 'cotizacion_id'); }
}
