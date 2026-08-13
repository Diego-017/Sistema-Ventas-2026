<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table    = 'lotes';
    protected $fillable = ['producto_id','compra_id','numero_lote','cantidad_inicial','cantidad_actual','costo_unitario','fecha_vencimiento'];
    const UPDATED_AT    = null;
    protected $casts    = ['fecha_vencimiento' => 'date'];

    public function producto() { return $this->belongsTo(Producto::class, 'producto_id'); }
    public function compra()   { return $this->belongsTo(Compra::class,   'compra_id'); }
}
