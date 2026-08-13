<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table    = 'notificaciones';
    protected $fillable = ['usuario_id','tipo','titulo','mensaje','url','leida'];
    const UPDATED_AT    = null;

    public function usuario() { return $this->belongsTo(Usuario::class, 'usuario_id'); }

    public static function generarAlertas(): void
    {
        // Stock bajo
        $stockBajo = Producto::activo()->lowStock()->get();
        foreach ($stockBajo as $p) {
            static::firstOrCreate(
                ['tipo' => 'stock_bajo', 'leida' => false, 'mensaje' => "Stock bajo: {$p->nombre}"],
                ['titulo' => '⚠️ Stock Bajo', 'url' => '/inventario/stock']
            );
        }
        // Créditos vencidos
        $vencidos = Credito::where('estado','vencido')->count();
        if ($vencidos > 0) {
            static::firstOrCreate(
                ['tipo' => 'credito_vencido', 'leida' => false],
                ['titulo' => '🔴 Créditos Vencidos', 'mensaje' => "{$vencidos} crédito(s) vencido(s)", 'url' => '/creditos']
            );
        }
    }
}
