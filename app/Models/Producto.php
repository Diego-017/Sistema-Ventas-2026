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
        'exento','es_combo','tipo_comision','valor_comision',
    ];
    protected $casts = [
        'activo'   => 'boolean',
        'exento'   => 'boolean',
        'es_combo' => 'boolean',
    ];

    public function categoria()    { return $this->belongsTo(Categoria::class,    'categoria_id'); }
    public function subcategoria() { return $this->belongsTo(Subcategoria::class, 'subcategoria_id'); }
    public function proveedor()    { return $this->belongsTo(Proveedor::class,    'proveedor_id'); }
    public function lotes()        { return $this->hasMany(Lote::class,           'producto_id'); }
    public function presentaciones() { return $this->hasMany(ProductoPresentacion::class, 'producto_id'); }
    public function comboItems()   { return $this->hasMany(ProductoComboItem::class, 'producto_id'); }

    /** Precio final que paga el cliente: base + 13% IVA (o igual al base si el producto es exento). */
    public function getPrecioConIvaAttribute(): float
    {
        return $this->exento ? (float) $this->precio_venta : round(((float) $this->precio_venta) * 1.13, 2);
    }

    /** Calcula el costo total de un combo sumando sus componentes (solo informativo). */
    public function getCostoComboAttribute(): float
    {
        if (!$this->es_combo) return (float) $this->precio_compra;
        return (float) $this->comboItems()
            ->with('componente')
            ->get()
            ->sum(fn($item) => $item->cantidad * ($item->componente->precio_compra ?? 0));
    }

    public function getComisionCalculada(float $montoVenta): float
    {
        return match ($this->tipo_comision) {
            'porcentaje'  => round($montoVenta * ($this->valor_comision / 100), 2),
            'monto_fijo'  => (float) $this->valor_comision,
            default       => 0.0,
        };
    }

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
