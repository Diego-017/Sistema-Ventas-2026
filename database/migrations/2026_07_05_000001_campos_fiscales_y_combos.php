<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega los campos fiscales que el negocio realmente necesita para operar
 * conforme a la Ley de IVA de El Salvador (13%, ventas exentas Art. 45/46,
 * percepción 1% Art. 163, retención 1% Art. 162) y para poder distinguir
 * clientes/proveedores de "mostrador" (consumidor final) de clientes/
 * proveedores con datos fiscales completos (NIT/NRC/DUI) para CCF.
 *
 * IMPORTANTE: esto NO implementa la transmisión real de DTE a Hacienda
 * (eso requiere un certificado digital y un proveedor autorizado por el
 * Ministerio de Hacienda). Lo que sí hace es calcular y guardar los montos
 * correctamente para que, cuando conecten un proveedor de DTE, los datos
 * ya estén completos y bien separados.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $t) {
            if (!Schema::hasColumn('clientes','tipo'))            $t->enum('tipo', ['contado','credito'])->default('contado')->after('nombre');
            if (!Schema::hasColumn('clientes','limite_credito'))  $t->decimal('limite_credito', 10, 2)->default(0)->after('tipo');
            if (!Schema::hasColumn('clientes','dias_credito'))    $t->integer('dias_credito')->default(30)->after('limite_credito');
            if (!Schema::hasColumn('clientes','nombre_comercial'))$t->string('nombre_comercial', 150)->nullable()->after('nombre');
            if (!Schema::hasColumn('clientes','tipo_documento'))  $t->enum('tipo_documento', ['dui','nit','pasaporte','carnet_residente','otro'])->default('dui')->after('nit');
            if (!Schema::hasColumn('clientes','dui'))             $t->string('dui', 15)->nullable()->after('tipo_documento');
            if (!Schema::hasColumn('clientes','nrc'))             $t->string('nrc', 15)->nullable()->after('nit');
            if (!Schema::hasColumn('clientes','giro'))            $t->string('giro', 150)->nullable()->after('nrc');
            if (!Schema::hasColumn('clientes','departamento'))    $t->string('departamento', 60)->nullable()->after('direccion');
            if (!Schema::hasColumn('clientes','municipio'))       $t->string('municipio', 60)->nullable()->after('departamento');
            if (!Schema::hasColumn('clientes','retiene_iva'))     $t->boolean('retiene_iva')->default(false)->after('giro');
            if (!Schema::hasColumn('clientes','retiene_renta'))   $t->boolean('retiene_renta')->default(false)->after('retiene_iva');
            if (!Schema::hasColumn('clientes','aplica_percepcion'))$t->boolean('aplica_percepcion')->default(false)->after('retiene_renta');
            if (!Schema::hasColumn('clientes','comisiona'))       $t->boolean('comisiona')->default(false)->after('aplica_percepcion');
            if (!Schema::hasColumn('clientes','activo'))          $t->boolean('activo')->default(true);
        });

        Schema::table('proveedores', function (Blueprint $t) {
            if (!Schema::hasColumn('proveedores','razon_social'))  $t->string('razon_social', 200)->nullable()->after('nombre');
            if (!Schema::hasColumn('proveedores','tipo_documento'))$t->enum('tipo_documento', ['dui','nit'])->default('nit')->after('razon_social');
            if (!Schema::hasColumn('proveedores','nit'))          $t->string('nit', 20)->nullable()->after('tipo_documento');
            if (!Schema::hasColumn('proveedores','nrc'))          $t->string('nrc', 15)->nullable()->after('nit');
            if (!Schema::hasColumn('proveedores','dui'))          $t->string('dui', 15)->nullable()->after('nrc');
            if (!Schema::hasColumn('proveedores','giro'))         $t->string('giro', 150)->nullable()->after('dui');
            if (!Schema::hasColumn('proveedores','departamento')) $t->string('departamento', 60)->nullable()->after('direccion');
            if (!Schema::hasColumn('proveedores','municipio'))    $t->string('municipio', 60)->nullable()->after('departamento');
            if (!Schema::hasColumn('proveedores','aplica_percepcion')) $t->boolean('aplica_percepcion')->default(false)->after('giro');
            if (!Schema::hasColumn('proveedores','aplica_retencion_renta')) $t->boolean('aplica_retencion_renta')->default(false)->after('aplica_percepcion');
            if (!Schema::hasColumn('proveedores','activo'))       $t->boolean('activo')->default(true);
        });

        Schema::table('productos', function (Blueprint $t) {
            if (!Schema::hasColumn('productos','exento'))       $t->boolean('exento')->default(false)->after('activo');
            if (!Schema::hasColumn('productos','es_combo'))     $t->boolean('es_combo')->default(false)->after('exento');
            if (!Schema::hasColumn('productos','tipo_comision')) $t->enum('tipo_comision', ['ninguno','porcentaje','monto_fijo'])->default('ninguno')->after('es_combo');
            if (!Schema::hasColumn('productos','valor_comision')) $t->decimal('valor_comision', 10, 2)->default(0)->after('tipo_comision');
        });

        Schema::table('ventas', function (Blueprint $t) {
            if (!Schema::hasColumn('ventas','venta_exenta')) $t->decimal('venta_exenta', 10, 2)->default(0)->after('impuesto');
            if (!Schema::hasColumn('ventas','percepcion'))   $t->decimal('percepcion', 10, 2)->default(0)->after('venta_exenta');
            if (!Schema::hasColumn('ventas','retencion'))    $t->decimal('retencion', 10, 2)->default(0)->after('percepcion');
        });

        // Componentes de un producto "combo" (ej: "Combo Desayuno" = 2 productos)
        if (!Schema::hasTable('producto_combo_items')) {
            Schema::create('producto_combo_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('producto_id')->constrained('productos')->cascadeOnDelete(); // el combo
                $t->foreignId('componente_id')->constrained('productos')->cascadeOnDelete(); // producto que lo compone
                $t->integer('cantidad')->default(1);
            });
        }

        // Presentaciones múltiples por producto (ej: Unidad / Caja x24 / Six-pack)
        if (!Schema::hasTable('producto_presentaciones')) {
            Schema::create('producto_presentaciones', function (Blueprint $t) {
                $t->id();
                $t->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
                $t->string('nombre', 60);          // "Caja x24", "Six Pack", "Unidad"
                $t->decimal('factor', 10, 2)->default(1); // cuántas unidades base equivale (24, 6, 1...)
                $t->decimal('precio', 10, 2);
                $t->boolean('es_base')->default(false);
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $t) {
            foreach (['venta_exenta','percepcion','retencion'] as $c) {
                if (Schema::hasColumn('ventas', $c)) $t->dropColumn($c);
            }
        });
        Schema::dropIfExists('producto_presentaciones');
        Schema::dropIfExists('producto_combo_items');

        Schema::table('productos', function (Blueprint $t) {
            foreach (['exento','es_combo','tipo_comision','valor_comision'] as $c) {
                if (Schema::hasColumn('productos', $c)) $t->dropColumn($c);
            }
        });
        Schema::table('proveedores', function (Blueprint $t) {
            foreach (['razon_social','tipo_documento','nit','nrc','dui','giro','departamento','municipio','aplica_percepcion','aplica_retencion_renta','activo'] as $c) {
                if (Schema::hasColumn('proveedores', $c)) $t->dropColumn($c);
            }
        });
        Schema::table('clientes', function (Blueprint $t) {
            foreach (['tipo','limite_credito','dias_credito','nombre_comercial','tipo_documento','dui','nrc','giro','departamento','municipio','retiene_iva','retiene_renta','aplica_percepcion','comisiona','activo'] as $c) {
                if (Schema::hasColumn('clientes', $c)) $t->dropColumn($c);
            }
        });
    }
};
