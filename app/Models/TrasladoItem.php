<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TrasladoItem extends Model
{
    protected $table    = 'traslado_items';
    protected $fillable = ['traslado_id','producto_id','cantidad_antes','cantidad_ajuste','cantidad_despues','notas'];
    public $timestamps  = false;

    public function producto()  { return $this->belongsTo(Producto::class,  'producto_id'); }
    public function traslado()  { return $this->belongsTo(Traslado::class,  'traslado_id'); }
}
