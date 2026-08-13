<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('categorias', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 100);
            $t->text('descripcion')->nullable();
        });
        Schema::create('proveedores', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 150);
            $t->string('contacto', 100)->nullable();
            $t->string('telefono', 30)->nullable();
            $t->string('email', 150)->nullable();
            $t->text('direccion')->nullable();
        });
        Schema::create('clientes', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 150);
            $t->string('email', 150)->nullable();
            $t->string('telefono', 30)->nullable();
            $t->text('direccion')->nullable();
            $t->string('nit', 30)->nullable();
        });
    }
    public function down(): void {
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('categorias');
    }
};
