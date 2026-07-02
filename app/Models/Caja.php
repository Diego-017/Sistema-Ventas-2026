<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table    = 'cajas';
    protected $fillable = ['nombre','usuario_id','monto_apertura','monto_cierre','total_ventas','total_gastos','estado','notas_apertura','notas_cierre','cerrada_at'];
    const UPDATED_AT    = null;
    const CREATED_AT    = 'abierta_at';

    public function usuario()     { return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function movimientos() { return $this->hasMany(Gasto::class,     'caja_id'); }
    public function ventas()      { return $this->hasMany(Venta::class,     'caja_id'); }
    public function cortes()      { return $this->hasMany(CorteCaja::class, 'caja_id'); }

    public static function abierta(): ?static
    {
        return static::where('estado','abierta')->latest('abierta_at')->first();
    }

    public function getBalanceEstimadoAttribute(): float
    {
        return $this->monto_apertura + $this->total_ventas - $this->total_gastos;
    }
}
