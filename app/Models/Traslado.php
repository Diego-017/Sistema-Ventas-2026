<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $table    = 'traslados';
    protected $fillable = ['usuario_id','concepto','tipo','notas'];
    const UPDATED_AT    = null;

    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }
    public function items()   { return $this->hasMany(TrasladoItem::class, 'traslado_id'); }
}
