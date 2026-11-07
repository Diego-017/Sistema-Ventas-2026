<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table    = 'usuarios';
    protected $fillable = ['nombre', 'email', 'password', 'rol', 'activo', 'telefono', 'cargo', 'sucursal', 'direccion', 'notas'];
    protected $hidden   = ['password'];

    public function isAdmin(): bool { return $this->rol === 'admin'; }
    public function ventas() { return $this->hasMany(Venta::class, 'usuario_id'); }
    public function getNameAttribute(): string { return $this->nombre ?? ''; }
}
