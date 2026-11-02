@extends('layouts.app')

@section('title', 'Panel general')
@section('body-class', 'suzuka-app')

@section('content')
@include('dashboard-concepts.partials.suzuka-sakura-live', ['operationalDashboard' => true])
@endsection
