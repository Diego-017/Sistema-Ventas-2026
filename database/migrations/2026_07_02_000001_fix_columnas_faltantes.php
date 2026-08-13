<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Corrige inconsistencias entre las migraciones originales y el código
 * (controladores/vistas) que ya asumía estas columnas:
 *
 *  - cajas:  faltaba `nombre` (CajaController@abrir lo inserta, la vista lo muestra)
 *  - ventas: faltaban `caja_id`, `impuesto`, `tipo_venta`
 *            (VentaController@store los guarda, CajaController@cerrar
 *             filtra ventas por caja_id, ventas/show.blade.php muestra tipo_venta)
 *  - gastos: el enum `tipo` no incluía 'egreso', pero CajaController y la
 *            vista de caja SIEMPRE envían 'egreso' (nunca 'gasto') → esto
 *            producía el error SQLSTATE 1265 "Data truncated for column 'tipo'".
 */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('cajas', 'nombre')) {
            Schema::table('cajas', function (Blueprint $t) {
                $t->string('nombre', 100)->default('Caja Principal')->after('id');
            });
        }

        Schema::table('ventas', function (Blueprint $t) {
            if (!Schema::hasColumn('ventas', 'caja_id')) {
                $t->foreignId('caja_id')->nullable()->after('usuario_id')
                  ->constrained('cajas')->nullOnDelete();
            }
            if (!Schema::hasColumn('ventas', 'impuesto')) {
                $t->decimal('impuesto', 10, 2)->default(0)->after('descuento');
            }
            if (!Schema::hasColumn('ventas', 'tipo_venta')) {
                $t->enum('tipo_venta', ['contado', 'credito'])->default('contado')->after('metodo_pago');
            }
        });

        // El enum original de `gastos.tipo` no incluía 'egreso', pero CajaController
        // y la vista de caja SIEMPRE envían 'egreso' (nunca 'gasto') → esto producía
        // el error SQLSTATE 1265 "Data truncated for column 'tipo'".
        //
        // En vez de ampliar el ENUM (sintaxis distinta por motor de BD y requiere
        // doctrine/dbal para usar ->change()), se reconstruye la tabla con `tipo`
        // como string libre. La validación de los valores permitidos ya se hace en
        // CajaController@registrarGasto ('required|in:egreso,retiro,ingreso'), así
        // que la restricción a nivel de aplicación es suficiente y más flexible.
        if (Schema::hasTable('gastos')) {
            Schema::create('gastos_tmp', function (Blueprint $t) {
                $t->id();
                $t->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
                $t->foreignId('usuario_id')->constrained('usuarios');
                $t->string('concepto', 200);
                $t->decimal('monto', 10, 2);
                $t->string('tipo', 20)->default('egreso');
                $t->timestamp('created_at')->nullable();
            });
            DB::statement('INSERT INTO gastos_tmp (id, caja_id, usuario_id, concepto, monto, tipo, created_at)
                            SELECT id, caja_id, usuario_id, concepto, monto, tipo, created_at FROM gastos');
            Schema::drop('gastos');
            Schema::rename('gastos_tmp', 'gastos');
        }
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $t) {
            if (Schema::hasColumn('ventas', 'caja_id'))    $t->dropConstrainedForeignId('caja_id');
            if (Schema::hasColumn('ventas', 'impuesto'))   $t->dropColumn('impuesto');
            if (Schema::hasColumn('ventas', 'tipo_venta')) $t->dropColumn('tipo_venta');
        });
        if (Schema::hasColumn('cajas', 'nombre')) {
            Schema::table('cajas', fn (Blueprint $t) => $t->dropColumn('nombre'));
        }
        // No se revierte `gastos.tipo` a ENUM: la validación ya vive en el controlador
        // y forzar de vuelta un ENUM estricto no aporta ningún beneficio real.
    }
};
