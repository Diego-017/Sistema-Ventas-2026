<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    protected $table    = 'cortes_caja';
    protected $fillable = [
        'caja_id','usuario_id','efectivo_esperado','efectivo_contado',
        'diferencia','ventas_efectivo','ventas_tarjeta','ventas_transferencia',
        'ventas_credito','denominaciones','notas'
    ];
    const UPDATED_AT = null;

    public function caja()    { return $this->belongsTo(Caja::class,    'caja_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }

    public function getDenominacionesArrayAttribute(): array
    {
        return $this->denominaciones ? json_decode($this->denominaciones, true) : [];
    }
}
