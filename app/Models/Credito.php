<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    protected $table    = 'creditos';
    protected $fillable = ['venta_id','cliente_id','usuario_id','monto_total','monto_pagado','saldo','estado','fecha_vencimiento','notas'];
    const UPDATED_AT    = null;
    protected $casts    = ['fecha_vencimiento' => 'date'];

    public function venta()   { return $this->belongsTo(Venta::class,   'venta_id'); }
    public function cliente() { return $this->belongsTo(Cliente::class, 'cliente_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function pagos()   { return $this->hasMany(PagoCredito::class, 'credito_id'); }

    public function estaVencido(): bool
    {
        return $this->estado === 'pendiente'
            && $this->fecha_vencimiento
            && $this->fecha_vencimiento->isPast();
    }
}
