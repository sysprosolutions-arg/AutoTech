@extends('layouts.app')

@section('title', 'Propuestas visuales')
@section('context', 'Laboratorio de diseño')
@section('body-class', 'design-lab-index')

@section('content')
<section class="design-lab-hero">
    <div>
        <div class="page-eyebrow">AutoTech Design Lab</div>
        <h1>Veintisiete direcciones visuales.<br><em>Una misma operación.</em></h1>
        <p>Cada propuesta reorganiza los mismos datos clave con una personalidad, ritmo y sistema cromático propios.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Volver al panel actual</a>
</section>

<div class="concept-gallery" aria-label="Veintisiete propuestas visuales para AutoTech">
    @foreach ($concepts as $concept)
        <a class="concept-gallery-card preview-{{ $concept['slug'] }}" href="{{ route('dashboard.concepts.show', $concept['slug']) }}">
            <div class="concept-preview-window" aria-hidden="true">
                <div class="concept-preview-rail"><span></span><i></i><i></i><i></i><i></i></div>
                <div class="concept-preview-main">
                    <div class="concept-preview-top"><span></span><b></b></div>
                    <div class="concept-preview-heading"><span></span><span></span></div>
                    <div class="concept-preview-metrics"><i></i><i></i><i></i></div>
                    <div class="concept-preview-chart"><span></span><span></span><span></span><span></span><span></span><span></span></div>
                </div>
            </div>
            <div class="concept-gallery-copy">
                <span>{{ $concept['number'] }} · {{ $concept['tagline'] }}</span>
                <h2>{{ $concept['name'] }}</h2>
                <p>{{ $concept['description'] }}</p>
                <b>Explorar propuesta <i class="bi bi-arrow-up-right" aria-hidden="true"></i></b>
            </div>
        </a>
    @endforeach
</div>
@endsection
