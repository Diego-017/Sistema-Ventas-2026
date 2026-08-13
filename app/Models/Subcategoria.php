<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    protected $table    = 'subcategorias';
    protected $fillable = ['categoria_id', 'nombre', 'descripcion'];
    public $timestamps  = false;

    public function categoria() { return $this->belongsTo(Categoria::class, 'categoria_id'); }
    public function productos() { return $this->hasMany(Producto::class, 'subcategoria_id'); }
}
