<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table    = 'clientes';
    protected $fillable = ['nombre','email','telefono','direccion','nit','tipo','limite_credito'];
    public $timestamps  = false;

    public function ventas()   { return $this->hasMany(Venta::class,   'cliente_id'); }
    public function creditos() { return $this->hasMany(Credito::class, 'cliente_id'); }

    public function getSaldoCreditoAttribute(): float
    {
        return (float) $this->creditos()->whereIn('estado',['pendiente','vencido'])->sum('saldo');
    }

    public function getCreditoDisponibleAttribute(): float
    {
        return max(0, $this->limite_credito - $this->saldo_credito);
    }

    public function puedeComprarCredito(float $monto): bool
    {
        return $this->tipo === 'credito' && $this->credito_disponible >= $monto;
    }
}
