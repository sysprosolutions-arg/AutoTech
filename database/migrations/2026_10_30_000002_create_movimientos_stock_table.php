<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('repuesto_id')->constrained('repuestos')->restrictOnDelete();
            $table->foreignId('registrado_por')->constrained('users')->restrictOnDelete();
            $table->string('operacion', 20);
            $table->unsignedInteger('cantidad');
            $table->unsignedInteger('stock_anterior');
            $table->unsignedInteger('stock_resultante');
            $table->string('motivo', 500);
            $table->timestamps();
            $table->index(['repuesto_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
