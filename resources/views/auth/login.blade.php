@extends('layouts.guest')

@section('title', 'Iniciar sesión')

@section('content')
<main class="auth-shell">
    <section class="auth-visual" aria-labelledby="auth-visual-title">
        <div class="d-flex align-items-center gap-3 position-relative z-1">
            <span class="brand-mark" aria-hidden="true">AT</span>
            <span class="brand-wordmark">AutoTech <small>Gestión integral del taller</small></span>
        </div>

        <div class="auth-copy">
            <div class="auth-eyebrow">Operación conectada</div>
            <h1 class="auth-title" id="auth-visual-title">Cada reparación, bajo control.</h1>
            <p class="auth-lead">
                Centralizá turnos, órdenes de trabajo, repuestos y facturación en una experiencia clara para todo el equipo.
            </p>

            <div class="auth-flow" aria-label="Flujo principal del sistema">
                <div class="flow-step"><i class="bi bi-calendar-check" aria-hidden="true"></i><span>Planificar</span></div>
                <div class="flow-step"><i class="bi bi-wrench-adjustable" aria-hidden="true"></i><span>Reparar</span></div>
                <div class="flow-step"><i class="bi bi-check2-circle" aria-hidden="true"></i><span>Entregar</span></div>
            </div>
        </div>

        <div class="auth-caption">Sistema de Información · AutoTech S.A.</div>
    </section>

    <section class="auth-form-panel" aria-labelledby="login-title">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="auth-eyebrow text-dark-emphasis">Acceso seguro</div>
                <h2 class="auth-card-title" id="login-title">Bienvenido</h2>
                <p class="auth-card-subtitle mb-0">Ingresá tus credenciales para acceder a las funciones habilitadas.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success border-0 rounded-4" role="status">
                    <i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="username">Usuario</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-person auth-input-icon" aria-hidden="true"></i>
                        <input
                            class="form-control auth-control @error('username') is-invalid @enderror"
                            id="username"
                            name="username"
                            type="text"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            maxlength="60"
                            required
                            autofocus
                            aria-describedby="username-error"
                        >
                    </div>
                    @error('username')
                        <div class="invalid-feedback d-block mt-2" id="username-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-lock auth-input-icon" aria-hidden="true"></i>
                        <input
                            class="form-control auth-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            aria-describedby="password-error"
                        >
                        <button class="password-toggle" type="button" data-password-toggle aria-controls="password" aria-label="Mostrar contraseña" aria-pressed="false">
                            <i class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block mt-2" id="password-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                    <label class="form-check-label small text-secondary" for="remember">Mantener mi sesión iniciada</label>
                </div>

                <button class="btn btn-autotech w-100" type="submit">
                    Ingresar al sistema <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                </button>
            </form>

            <div class="auth-security-note">
                <i class="bi bi-shield-check fs-5" aria-hidden="true"></i>
                <span>El acceso está protegido. Los intentos fallidos se limitan automáticamente y las credenciales se almacenan de forma segura.</span>
            </div>
        </div>
    </section>
</main>
@endsection
