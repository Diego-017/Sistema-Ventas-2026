<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductoPresentacion extends Model
{
    protected $table    = 'producto_presentaciones';
    protected $fillable = ['producto_id','nombre','factor','precio','es_base'];
    protected $casts    = ['es_base' => 'boolean'];

    public function producto() { return $this->belongsTo(Producto::class, 'producto_id'); }
}
