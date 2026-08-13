<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductoComboItem extends Model
{
    protected $table    = 'producto_combo_items';
    protected $fillable = ['producto_id','componente_id','cantidad'];
    public $timestamps  = false;

    /** El combo al que pertenece este componente */
    public function combo()      { return $this->belongsTo(Producto::class, 'producto_id'); }

    /** El producto real que forma parte del combo */
    public function componente() { return $this->belongsTo(Producto::class, 'componente_id'); }
}
