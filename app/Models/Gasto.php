<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table    = 'gastos';
    protected $fillable = ['caja_id','usuario_id','concepto','monto','tipo'];
    const UPDATED_AT    = null;
    public function caja()    { return $this->belongsTo(Caja::class,    'caja_id'); }
    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
}
