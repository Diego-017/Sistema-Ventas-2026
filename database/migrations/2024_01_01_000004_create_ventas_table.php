<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ventas', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 20)->unique();
            $t->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('subtotal', 10, 2);
            $t->decimal('descuento', 10, 2)->default(0);
            $t->decimal('total', 10, 2);
            $t->enum('metodo_pago', ['efectivo','tarjeta','transferencia'])->default('efectivo');
            $t->enum('estado', ['completada','anulada','pendiente'])->default('completada');
            $t->text('notas')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('venta_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $t->foreignId('producto_id')->constrained('productos');
            $t->string('nombre_producto', 200);
            $t->integer('cantidad');
            $t->decimal('precio_unitario', 10, 2);
            $t->decimal('descuento', 10, 2)->default(0);
            $t->decimal('subtotal', 10, 2);
        });
    }
    public function down(): void {
        Schema::dropIfExists('venta_items');
        Schema::dropIfExists('ventas');
    }
};
