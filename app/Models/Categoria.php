<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table    = 'categorias';
    protected $fillable = ['codigo','nombre','descripcion','color','icono','margen_sugerido','activo','orden'];
    public $timestamps  = false;

    public function subcategorias() { return $this->hasMany(Subcategoria::class, 'categoria_id'); }
    public function productos()     { return $this->hasMany(Producto::class, 'categoria_id'); }
}
