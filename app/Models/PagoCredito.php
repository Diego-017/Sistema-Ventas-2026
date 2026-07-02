<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PagoCredito extends Model
{
    protected $table    = 'pagos_credito';
    protected $fillable = ['credito_id','usuario_id','monto','metodo','notas'];
    const UPDATED_AT    = null;

    public function credito() { return $this->belongsTo(Credito::class, 'credito_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
}
