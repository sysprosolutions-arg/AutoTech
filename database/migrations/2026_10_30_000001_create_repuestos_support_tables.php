<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table): void {
            $table->id();
            $table->string('cuit', 13)->unique();
            $table->string('razon_social', 150)->index();
            $table->string('nombre_fantasia', 150)->nullable();
            $table->string('localidad', 100)->index();
            $table->string('direccion', 200)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('repuestos', function (Blueprint $table): void {
            $table->foreignId('proveedor_id')
                ->nullable()
                ->after('id')
                ->constrained('proveedores')
                ->restrictOnDelete();
            $table->string('descripcion', 500)->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('repuestos', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('proveedor_id');
            $table->dropColumn('descripcion');
        });

        Schema::dropIfExists('proveedores');
    }
};
