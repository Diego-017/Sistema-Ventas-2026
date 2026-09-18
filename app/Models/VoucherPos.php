<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherPos extends Model
{
    protected $table = 'vouchers_pos';

    protected $fillable = [
        'caja_id',
        'usuario_id',
        'venta_id',
        'banco',
        'numero_lote',
        'numero_voucher',
        'monto',
        'comision_porcentaje',
        'comision_monto',
        'monto_neto',
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

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
