<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Historial de precios de productos
        Schema::create('precios_historial', function (Blueprint $t) {
            $t->id();
            $t->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('precio_compra_anterior',10,2)->default(0);
            $t->decimal('precio_compra_nuevo',10,2)->default(0);
            $t->decimal('precio_venta_anterior',10,2)->default(0);
            $t->decimal('precio_venta_nuevo',10,2)->default(0);
            $t->string('motivo',200)->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        // Configuración de impresoras
        Schema::create('impresoras', function (Blueprint $t) {
            $t->id();
            $t->string('nombre',100);
            $t->enum('tipo',['termica','laser','inkjet'])->default('termica');
            $t->string('conexion',100)->nullable()->comment('IP, USB, Bluetooth');
            $t->integer('ancho_papel')->default(80)->comment('mm');
            $t->boolean('activa')->default(true);
            $t->boolean('predeterminada')->default(false);
            $t->text('configuracion')->nullable()->comment('JSON extra');
            $t->timestamps();
        });

        // Cortes de caja con desglose
        Schema::create('cortes_caja', function (Blueprint $t) {
            $t->id();
            $t->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
            $t->foreignId('usuario_id')->constrained('usuarios');
            $t->decimal('efectivo_esperado',10,2)->default(0);
            $t->decimal('efectivo_contado',10,2)->default(0);
            $t->decimal('diferencia',10,2)->default(0);
            $t->decimal('ventas_efectivo',10,2)->default(0);
            $t->decimal('ventas_tarjeta',10,2)->default(0);
            $t->decimal('ventas_transferencia',10,2)->default(0);
            $t->decimal('ventas_credito',10,2)->default(0);
            $t->text('denominaciones')->nullable()->comment('JSON: {100:2, 50:3, ...}');
            $t->text('notas')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });

        // Alertas/Notificaciones del sistema
        Schema::create('notificaciones', function (Blueprint $t) {
            $t->id();
            $t->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $t->string('tipo',50)->comment('stock_bajo,credito_vencido,caja_sin_abrir');
            $t->string('titulo',200);
            $t->text('mensaje');
            $t->string('url',255)->nullable();
            $t->boolean('leida')->default(false);
            $t->timestamp('created_at')->useCurrent();
        });

        // Tipo de cliente en tabla clientes
        Schema::table('clientes', function (Blueprint $t) {
            $t->enum('tipo',['contado','credito'])->default('contado')->after('nit');
            $t->decimal('limite_credito',10,2)->default(0)->after('tipo');
        });

        // Campos extra en productos
        Schema::table('productos', function (Blueprint $t) {
           //  $t->decimal('precio_mayoreo',10,2)->default(0)->after('precio_venta')->nullable();
          //  $t->string('unidad',30)->default('unidad')->after('stock_minimo')->nullable();
         //   $t->string('codigo_barras2',100)->nullable()->after('codigo_barras');
        });
    }

    public function down(): void {
        Schema::dropIfExists('notificaciones');
        Schema::dropIfExists('cortes_caja');
        Schema::dropIfExists('impresoras');
        Schema::dropIfExists('precios_historial');
        Schema::table('clientes', fn($t) => $t->dropColumn(['tipo','limite_credito']));
        Schema::table('productos', fn($t) => $t->dropColumn(['precio_mayoreo','unidad','codigo_barras2']));
    }
};
