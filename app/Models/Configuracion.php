<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table    = 'configuracion';
    protected $fillable = ['clave','valor'];
    public $timestamps  = false;
    const UPDATED_AT    = 'updated_at';

    public static function get(string $clave, mixed $default = null): mixed
    {
        $row = static::where('clave', $clave)->first();
        return $row ? $row->valor : $default;
    }
    public static function set(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
    public static function all_config(): array
    {
        return static::all()->pluck('valor','clave')->toArray();
    }
}
