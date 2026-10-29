<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\PresupuestoDecisionController;
use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\TurnoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('role:administrador,turnos')->prefix('turnos')->name('turnos.')->group(function (): void {
        Route::get('/', [TurnoController::class, 'index'])->name('index');
        Route::get('/crear', [TurnoController::class, 'create'])->name('create');
        Route::post('/', [TurnoController::class, 'store'])->name('store');
        Route::get('/{turno}/presupuesto/crear', [PresupuestoController::class, 'create'])->whereNumber('turno')->name('presupuesto.create');
        Route::get('/{turno}', [TurnoController::class, 'show'])->whereNumber('turno')->name('show');
    });

    Route::middleware('role:administrador,turnos')->prefix('presupuestos')->name('presupuestos.')->group(function (): void {
        Route::post('/', [PresupuestoController::class, 'store'])->name('store');
        Route::get('/{presupuesto}', [PresupuestoController::class, 'show'])->whereNumber('presupuesto')->name('show');
        Route::post('/{presupuesto}/decision', PresupuestoDecisionController::class)->whereNumber('presupuesto')->name('decision.store');
    });

    Route::middleware('role:administrador,reparacion')->prefix('reparaciones')->name('reparaciones.')->group(function (): void {
        Route::get('/', [ReparacionController::class, 'index'])->name('index');
        Route::get('/crear', [ReparacionController::class, 'create'])->name('create');
        Route::get('/crear/{ordenTrabajo}', [ReparacionController::class, 'createFromOrder'])->whereNumber('ordenTrabajo')->name('create.from-order');
        Route::post('/', [ReparacionController::class, 'store'])->name('store');
        Route::get('/{reparacion}', [ReparacionController::class, 'show'])->whereNumber('reparacion')->name('show');
        Route::post('/{reparacion}/finalizar', [ReparacionController::class, 'finish'])->whereNumber('reparacion')->name('finish');
    });
});
