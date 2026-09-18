<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id',
        'usuario_id',
        'tipo',
        'categoria',
        'descripcion',
        'comprobante',
        'monto',
        'pago_a',
        'estado',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Scopes de filtrado por rol/sucursal
    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeDelDia($query)
    {
        return $query->whereDate('created_at', today());
    }
}
