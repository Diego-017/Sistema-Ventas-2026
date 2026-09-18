<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Cuadraturas y Conciliaciones de Turno de Caja
        if (!Schema::hasTable('cuadraturas_caja')) {
            Schema::create('cuadraturas_caja', function (Blueprint $table) {
                $table->id();
                $table->foreignId('caja_id')->constrained('cajas')->cascadeOnDelete();
                $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
                $table->decimal('efectivo_sistema', 12, 2)->default(0);
                $table->decimal('efectivo_real', 12, 2)->default(0);
                $table->decimal('tarjeta_sistema', 12, 2)->default(0);
                $table->decimal('tarjeta_real_vouchers', 12, 2)->default(0);
                $table->decimal('diferencia', 12, 2)->default(0); // Positivo = sobrante, Negativo = faltante
                $table->enum('estado_cuadratura', ['cuadrada', 'con_descuadre'])->default('cuadrada');
                $table->text('observaciones')->nullable();
                $table->timestamps();
            });
        }

        // 2. Campo de fecha_vencimiento en lotes para control de expiración
        if (Schema::hasTable('lotes')) {
            Schema::table('lotes', function (Blueprint $table) {
                if (!Schema::hasColumn('lotes', 'fecha_vencimiento')) {
                    $table->date('fecha_vencimiento')->nullable()->after('codigo_lote');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cuadraturas_caja');
        if (Schema::hasTable('lotes') && Schema::hasColumn('lotes', 'fecha_vencimiento')) {
            Schema::table('lotes', function (Blueprint $table) {
                $table->dropColumn('fecha_vencimiento');
            });
        }
    }
};
