<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Movimientos de Caja Chica (Ingresos y Egresos por categoría)
        if (!Schema::hasTable('movimientos_caja')) {
            Schema::create('movimientos_caja', function (Blueprint $table) {
                $table->id();
                $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->enum('tipo', ['ingreso', 'egreso'])->default('ingreso');
                $table->string('categoria', 100); // ej: Venta Externa, Inyección, Viáticos, Servicios, Compra Menor
                $table->string('descripcion', 255);
                $table->string('comprobante', 100)->nullable(); // Factura, Recibo, CCF, Ticket
                $table->decimal('monto', 12, 2);
                $table->string('pago_a', 150)->nullable(); // Beneficiario o remitente
                $table->enum('estado', ['aprobado', 'pendiente', 'rechazado'])->default('aprobado');
                $table->timestamps();
            });
        }

        // 2. Vouchers de Tarjeta en Terminales POS Bancarias
        if (!Schema::hasTable('vouchers_pos')) {
            Schema::create('vouchers_pos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete();
                $table->string('banco', 100); // BAC, Bancoagrícola, Cuscatlán, Serfinsa, Davivienda, etc.
                $table->string('numero_lote', 50);
                $table->string('numero_voucher', 50);
                $table->decimal('monto', 12, 2);
                $table->decimal('comision_porcentaje', 5, 2)->default(0); // Ej: 2.5%
                $table->decimal('comision_monto', 10, 2)->default(0);
                $table->decimal('monto_neto', 12, 2);
                $table->enum('estado', ['registrado', 'conciliado', 'anulado'])->default('registrado');
                $table->timestamps();
            });
        }

        // 3. Bitácora de Auditoría de Actividad en Tiempo Real
        if (!Schema::hasTable('auditoria_actividades')) {
            Schema::create('auditoria_actividades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->string('modulo', 50); // Ventas, Caja, Inventario, Usuarios, etc.
                $table->string('accion', 100); // Registro Ingreso, Anulación Venta, Arqueo, etc.
                $table->text('detalle');
                $table->decimal('monto', 12, 2)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_actividades');
        Schema::dropIfExists('vouchers_pos');
        Schema::dropIfExists('movimientos_caja');
    }
};
