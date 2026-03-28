@extends('layouts.layout_menu_sidebar')

@section('title', 'Nuevo ' . $admin->getTitle())

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 fw-bold">Nuevo {{ $admin->getTitle() }}</h2>
            <p class="text-muted mb-0">Complete el formulario para crear un nuevo registro</p>
        </div>
        <a href="#" class="btn btn-outline-secondary">
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

    {{-- Form Card --}}
    <div class="content-card">
        <form method="{{ $form->getMethod() }}" action="{{ $form->getAction() }}">
            @csrf

            @foreach($form->getFields() as $field)
                <x-form.field :field="$field" />
            @endforeach

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Guardar
                </button>
                <a href="#" class="btn btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
