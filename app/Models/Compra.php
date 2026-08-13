<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table    = 'compras';
    protected $fillable = ['codigo','proveedor_id','usuario_id','total','estado','notas'];
    const UPDATED_AT    = null;

    public function proveedor() { return $this->belongsTo(Proveedor::class, 'proveedor_id'); }
    public function usuario()   { return $this->belongsTo(Usuario::class,   'usuario_id'); }
    public function items()     { return $this->hasMany(CompraItem::class,   'compra_id'); }
}
