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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 20)->unique();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('telefono', 40);
            $table->string('email')->nullable()->unique();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('patente', 12)->unique();
            $table->string('marca', 80);
            $table->string('modelo', 100);
            $table->unsignedSmallInteger('anio');
            $table->string('color', 60)->nullable();
            $table->boolean('activo')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('estados_turno', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 80);
            $table->timestamps();
        });

        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->foreignId('estado_turno_id')->constrained('estados_turno')->restrictOnDelete();
            $table->foreignId('registrado_por')->constrained('users')->restrictOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('motivo', 500);
            $table->string('observaciones', 1000)->nullable();
            $table->timestamps();

            $table->index(['fecha', 'hora']);
            $table->index(['cliente_id', 'vehiculo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
        Schema::dropIfExists('estados_turno');
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('clientes');
    }
};
