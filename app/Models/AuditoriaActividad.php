<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaActividad extends Model
{
    protected $table = 'auditoria_actividades';

    protected $fillable = [
        'usuario_id',
        'modulo',
        'accion',
        'detalle',
        'monto',
        'ip_address',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public static function registrar($modulo, $accion, $detalle, $monto = null)
    {
        $usuarioId = session('user.id') ?? 1;
        return self::create([
            'usuario_id' => $usuarioId,
            'modulo'     => $modulo,
            'accion'     => $accion,
            'detalle'    => $detalle,
            'monto'      => $monto,
            'ip_address' => request()->ip(),
        ]);
    }
}
