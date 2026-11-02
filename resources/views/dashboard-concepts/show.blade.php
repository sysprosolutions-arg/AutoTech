@extends('layouts.app')

@section('title', $concept['name'])
@section('context', 'Propuesta '.$concept['number'].' · '.$concept['name'])

@section('body-class')
dashboard-concept-body concept-{{ $concept['slug'] }}
@endsection

@section('content')
<div class="concept-workbench">
    <nav class="concept-switcher" aria-label="Cambiar propuesta visual">
        <a class="concept-switcher-back" href="{{ route('dashboard.concepts.index') }}"><i class="bi bi-grid-3x2-gap" aria-hidden="true"></i><span>Ver las 27</span></a>
        <div>
            @foreach ($concepts as $option)
                <a class="{{ $option['slug'] === $concept['slug'] ? 'active' : '' }}" href="{{ route('dashboard.concepts.show', $option['slug']) }}" title="{{ $option['name'] }}">{{ $option['number'] }}</a>
            @endforeach
        </div>
        <span class="concept-data-note"><i class="bi bi-info-circle" aria-hidden="true"></i> Datos ilustrativos</span>
    </nav>

    @include('dashboard-concepts.partials.'.$concept['slug'])
</div>
@endsection
