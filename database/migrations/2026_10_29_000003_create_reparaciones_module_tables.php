<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_reparacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 120);
            $table->string('descripcion', 500)->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('estados_reparacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 80);
            $table->timestamps();
        });

        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->nullable()->unique();
            $table->foreignId('orden_trabajo_id')->unique()->constrained('ordenes_trabajo')->restrictOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->foreignId('empleado_id')->constrained('empleados')->restrictOnDelete();
            $table->foreignId('tipo_reparacion_id')->constrained('tipos_reparacion')->restrictOnDelete();
            $table->foreignId('estado_reparacion_id')->constrained('estados_reparacion')->restrictOnDelete();
            $table->foreignId('registrado_por')->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->unsignedInteger('tiempo_empleado_minutos');
            $table->string('trabajos_realizados', 2000);
            $table->string('incidencias', 2000)->nullable();
            $table->timestamps();

            $table->index(['estado_reparacion_id', 'fecha_inicio']);
        });

        Schema::create('detalle_reparaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reparacion_id')->constrained('reparaciones')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->foreignId('tipo_servicio_id')->nullable()->constrained('tipos_servicio')->restrictOnDelete();
            $table->foreignId('repuesto_id')->nullable()->constrained('repuestos')->restrictOnDelete();
            $table->string('descripcion', 200);
            $table->unsignedSmallInteger('cantidad');
            $table->timestamps();

            $table->index(['reparacion_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_reparaciones');
        Schema::dropIfExists('reparaciones');
        Schema::dropIfExists('estados_reparacion');
        Schema::dropIfExists('tipos_reparacion');
    }
};
