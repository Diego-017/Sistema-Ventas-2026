<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Subcategorías
        Schema::create('subcategorias', function (Blueprint $t) {
            $t->id();
            $t->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $t->string('nombre', 100);
            $t->text('descripcion')->nullable();
        });

        // Agregar subcategoria_id y precio_mayoreo a productos
        Schema::table('productos', function (Blueprint $t) {
            $t->foreignId('subcategoria_id')->nullable()->after('categoria_id')
              ->constrained('subcategorias')->nullOnDelete();
            $t->decimal('precio_mayoreo', 10, 2)->default(0)->after('precio_venta');
            $t->string('unidad', 30)->default('unidad')->after('stock_minimo');
            $t->string('codigo_barras2', 100)->nullable()->after('codigo_barras');
        });

        // Lotes de inventario
        Schema::create('lotes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $t->foreignId('compra_id')->nullable()->constrained('compras')->nullOnDelete();
            $t->string('numero_lote', 50)->nullable();
            $t->integer('cantidad_inicial')->default(0);
            $t->integer('cantidad_actual')->default(0);
            $t->decimal('costo_unitario', 10, 2)->default(0);
            $t->date('fecha_vencimiento')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        // Traslados de inventario (ajustes)
        Schema::create('traslados', function (Blueprint $t) {
            $t->id();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->string('concepto', 200)->nullable();
            $t->enum('tipo', ['entrada', 'salida', 'ajuste'])->default('ajuste');
            $t->text('notas')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        Schema::create('traslado_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('traslado_id')->constrained('traslados')->cascadeOnDelete();
            $t->foreignId('producto_id')->constrained('productos');
            $t->integer('cantidad_antes');
            $t->integer('cantidad_ajuste');
            $t->integer('cantidad_despues');
            $t->text('notas')->nullable();
        });

        // Créditos / Cuentas por cobrar
        Schema::create('creditos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
            $t->foreignId('cliente_id')->constrained('clientes');
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('monto_total', 10, 2);
            $t->decimal('monto_pagado', 10, 2)->default(0);
            $t->decimal('saldo', 10, 2);
            $t->enum('estado', ['pendiente', 'pagado', 'vencido'])->default('pendiente');
            $t->date('fecha_vencimiento')->nullable();
            $t->text('notas')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        Schema::create('pagos_credito', function (Blueprint $t) {
            $t->id();
            $t->foreignId('credito_id')->constrained('creditos')->cascadeOnDelete();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('monto', 10, 2);
            $t->enum('metodo', ['efectivo', 'tarjeta', 'transferencia'])->default('efectivo');
            $t->text('notas')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        // Cotizaciones
        Schema::create('cotizaciones', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 20)->unique();
            $t->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('subtotal', 10, 2);
            $t->decimal('descuento', 10, 2)->default(0);
            $t->decimal('total', 10, 2);
            $t->enum('estado', ['vigente', 'vencida', 'convertida', 'cancelada'])->default('vigente');
            $t->date('valida_hasta')->nullable();
            $t->text('notas')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        Schema::create('cotizacion_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            $t->foreignId('producto_id')->constrained('productos');
            $t->string('nombre_producto', 200);
            $t->integer('cantidad');
            $t->decimal('precio_unitario', 10, 2);
            $t->decimal('subtotal', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_items');
        Schema::dropIfExists('cotizaciones');
        Schema::dropIfExists('pagos_credito');
        Schema::dropIfExists('creditos');
        Schema::dropIfExists('traslado_items');
        Schema::dropIfExists('traslados');
        Schema::dropIfExists('lotes');
        Schema::table('productos', function (Blueprint $t) {
            $t->dropColumn(['subcategoria_id', 'precio_mayoreo', 'unidad', 'codigo_barras2']);
        });
        Schema::dropIfExists('subcategorias');
    }
};
