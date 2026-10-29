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
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 40)->unique();
            $table->string('nombre', 100);
            $table->timestamps();
        });

        Schema::create('estados_empleado', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 80);
            $table->timestamps();
        });

        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('legajo', 30)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->foreignId('especialidad_id')->constrained('especialidades')->restrictOnDelete();
            $table->foreignId('estado_empleado_id')->constrained('estados_empleado')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('tipos_servicio', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 120);
            $table->string('descripcion', 500)->nullable();
            $table->foreignId('especialidad_id')->constrained('especialidades')->restrictOnDelete();
            $table->decimal('precio_base', 12, 2);
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->string('marca', 100);
            $table->boolean('original')->default(false)->index();
            $table->decimal('precio_venta', 12, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->nullable()->unique();
            $table->foreignId('turno_id')->unique()->constrained('turnos')->restrictOnDelete();
            $table->foreignId('registrado_por')->constrained('users')->restrictOnDelete();
            $table->date('fecha_emision');
            $table->date('valido_hasta');
            $table->decimal('total', 12, 2);
            $table->string('observaciones', 1000)->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presupuesto_id')->constrained('presupuestos')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->foreignId('tipo_servicio_id')->nullable()->constrained('tipos_servicio')->restrictOnDelete();
            $table->foreignId('repuesto_id')->nullable()->constrained('repuestos')->restrictOnDelete();
            $table->string('descripcion', 200);
            $table->unsignedSmallInteger('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->index(['presupuesto_id', 'tipo']);
        });

        Schema::create('aprobaciones_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presupuesto_id')->unique()->constrained('presupuestos')->cascadeOnDelete();
            $table->string('estado', 20)->default('pendiente')->index();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_decision')->nullable();
            $table->string('medio_confirmacion', 30)->nullable();
            $table->string('observaciones', 1000)->nullable();
            $table->timestamps();
        });

        Schema::create('estados_orden_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 80);
            $table->timestamps();
        });

        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30)->nullable()->unique();
            $table->foreignId('presupuesto_id')->unique()->constrained('presupuestos')->restrictOnDelete();
            $table->foreignId('turno_id')->unique()->constrained('turnos')->restrictOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->foreignId('empleado_id')->constrained('empleados')->restrictOnDelete();
            $table->foreignId('estado_orden_trabajo_id')->constrained('estados_orden_trabajo')->restrictOnDelete();
            $table->foreignId('emitida_por')->constrained('users')->restrictOnDelete();
            $table->dateTime('fecha_emision');
            $table->string('instrucciones', 1000)->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
            $table->foreignId('tipo_servicio_id')->nullable()->constrained('tipos_servicio')->restrictOnDelete();
            $table->foreignId('repuesto_id')->nullable()->constrained('repuestos')->restrictOnDelete();
            $table->string('descripcion', 200);
            $table->unsignedSmallInteger('cantidad');
            $table->decimal('precio_referencia', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ordenes_trabajo');
        Schema::dropIfExists('ordenes_trabajo');
        Schema::dropIfExists('estados_orden_trabajo');
        Schema::dropIfExists('aprobaciones_presupuesto');
        Schema::dropIfExists('detalle_presupuestos');
        Schema::dropIfExists('presupuestos');
        Schema::dropIfExists('repuestos');
        Schema::dropIfExists('tipos_servicio');
        Schema::dropIfExists('empleados');
        Schema::dropIfExists('estados_empleado');
        Schema::dropIfExists('especialidades');
    }
};
