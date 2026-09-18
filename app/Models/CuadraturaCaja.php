<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuadraturaCaja extends Model
{
    protected $table = 'cuadraturas_caja';

    protected $fillable = [
        'caja_id',
        'usuario_id',
        'efectivo_sistema',
        'efectivo_real',
        'tarjeta_sistema',
        'tarjeta_real_vouchers',
        'diferencia',
        'estado_cuadratura',
        'observaciones',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
