<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Producto extends Model
{
    protected $table    = 'productos';
    protected $fillable = [
        'sku','codigo_barras','codigo_barras2','nombre','descripcion',
        'categoria_id','subcategoria_id','proveedor_id',
        'precio_compra','precio_venta','precio_mayoreo',
        'stock','stock_minimo','unidad','imagen','activo',
    ];

    public function categoria()    { return $this->belongsTo(Categoria::class,    'categoria_id'); }
    public function subcategoria() { return $this->belongsTo(Subcategoria::class, 'subcategoria_id'); }
    public function proveedor()    { return $this->belongsTo(Proveedor::class,    'proveedor_id'); }
    public function lotes()        { return $this->hasMany(Lote::class,           'producto_id'); }

    public function scopeActivo(Builder $q): Builder   { return $q->where('activo', 1); }
    public function scopeLowStock(Builder $q): Builder { return $q->whereRaw('stock <= stock_minimo'); }
    public function scopeSearch(Builder $q, string $term): Builder {
        return $q->where(function (Builder $s) use ($term) {
            $s->where('nombre','like',"%{$term}%")
              ->orWhere('sku','like',"%{$term}%")
              ->orWhere('codigo_barras','like',"%{$term}%")
              ->orWhere('codigo_barras2','like',"%{$term}%");
        });
    }

    public function getMargenAttribute(): float
    {
        return $this->precio_venta - $this->precio_compra;
    }

    public function getMargenPorcentajeAttribute(): float
    {
        if ($this->precio_compra <= 0) return 0;
        return round((($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100, 2);
    }
}
