@extends('layouts.layout_menu_sidebar')

@section('title', $config->getTitle())

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 fw-bold">{{ $config->getTitle() }}</h2>
            <p class="text-muted mb-0">Complete el formulario para crear un nuevo registro</p>
        </div>
        <a href="{{ $admin->getUrl('list') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Volver
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Hay errores en el formulario:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="content-card">
        <form method="POST" action="{{ $admin->getUrl('create') }}">
            @csrf

            @foreach($config->getFormBuilder()->getFields() as $field)
                <x-form.field :field="$field" />
            @endforeach

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>{{ $config->getSubmitLabel() }}
                </button>
                <a href="{{ $admin->getUrl('list') }}" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
