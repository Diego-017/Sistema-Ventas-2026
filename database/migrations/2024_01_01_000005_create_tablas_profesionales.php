<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $t) {
            $t->id();
            $t->string('clave', 100)->unique();
            $t->text('valor')->nullable();
            $t->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('compras', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 20)->unique();
            $t->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('total', 10, 2);
            $t->enum('estado', ['completada','pendiente','anulada'])->default('completada');
            $t->text('notas')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        Schema::create('compra_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();
            $t->foreignId('producto_id')->constrained('productos');
            $t->string('nombre_producto', 200);
            $t->integer('cantidad');
            $t->decimal('precio_unitario', 10, 2);
            $t->decimal('subtotal', 10, 2);
        });

        Schema::create('cajas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('monto_apertura', 10, 2)->default(0);
            $t->decimal('monto_cierre', 10, 2)->nullable();
            $t->decimal('total_ventas', 10, 2)->default(0);
            $t->decimal('total_gastos', 10, 2)->default(0);
            $t->enum('estado', ['abierta','cerrada'])->default('abierta');
            $t->text('notas_apertura')->nullable();
            $t->text('notas_cierre')->nullable();
            $t->timestamp('abierta_at')->useCurrent();
            $t->timestamp('cerrada_at')->nullable();
        });

        Schema::create('gastos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->string('concepto', 200);
            $t->decimal('monto', 10, 2);
            $t->enum('tipo', ['gasto','retiro','ingreso'])->default('gasto');
            $t->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
        Schema::dropIfExists('cajas');
        Schema::dropIfExists('compra_items');
        Schema::dropIfExists('compras');
        Schema::dropIfExists('configuracion');
    }
};
