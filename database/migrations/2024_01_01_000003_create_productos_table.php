<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('productos', function (Blueprint $t) {
            $t->id();
            $t->string('sku', 50)->nullable()->unique();
            $t->string('codigo_barras', 100)->nullable();
            $t->string('nombre', 200);
            $t->text('descripcion')->nullable();
            $t->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $t->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $t->decimal('precio_compra', 10, 2)->default(0);
            $t->decimal('precio_venta', 10, 2);
            $t->integer('stock')->default(0);
            $t->integer('stock_minimo')->default(5);
            $t->string('imagen', 255)->nullable();
            $t->boolean('activo')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('productos'); }
};
