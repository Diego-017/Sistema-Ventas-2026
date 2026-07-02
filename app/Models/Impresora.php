<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Impresora extends Model
{
    protected $table    = 'impresoras';
    protected $fillable = ['nombre','tipo','conexion','ancho_papel','activa','predeterminada','configuracion'];

    public static function predeterminada(): ?static
    {
        return static::where('activa',1)->where('predeterminada',1)->first()
            ?? static::where('activa',1)->first();
    }
}
