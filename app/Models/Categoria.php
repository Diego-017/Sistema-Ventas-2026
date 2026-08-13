<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table    = 'categorias';
    protected $fillable = ['nombre','descripcion'];
    public $timestamps  = false;

    public function subcategorias() { return $this->hasMany(Subcategoria::class, 'categoria_id'); }
    public function productos()     { return $this->hasMany(Producto::class, 'categoria_id'); }
}
