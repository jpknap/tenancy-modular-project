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
                @if($field['type'] === 'hidden')
                    <input type="hidden" name="{{ $field['name'] }}" value="{{ $field['value'] ?? '' }}">

                @elseif($field['type'] === 'textarea')
                    <div class="mb-3">
                        <label for="{{ $field['name'] }}" class="form-label">
                            {{ $field['label'] }}
                            @if($field['options']['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <textarea
                            class="form-control @error($field['name']) is-invalid @enderror"
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            rows="{{ $field['options']['rows'] ?? 3 }}"
                            placeholder="{{ $field['options']['placeholder'] ?? '' }}"
                            {{ ($field['options']['required'] ?? false) ? 'required' : '' }}
                        >{{ old($field['name']) }}</textarea>
                        @error($field['name'])
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @elseif($field['type'] === 'select')
                    <div class="mb-3">
                        <label for="{{ $field['name'] }}" class="form-label">
                            {{ $field['label'] }}
                            @if($field['options']['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <select
                            class="form-select @error($field['name']) is-invalid @enderror"
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            {{ ($field['options']['required'] ?? false) ? 'required' : '' }}
                        >
                            <option value="">Seleccione una opción</option>
                            @foreach($field['choices'] as $value => $label)
                                <option value="{{ $value }}" {{ old($field['name']) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error($field['name'])
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @elseif($field['type'] === 'checkbox')
                    <div class="mb-3 form-check">
                        <input
                            type="checkbox"
                            class="form-check-input @error($field['name']) is-invalid @enderror"
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            value="1"
                            {{ old($field['name'], $field['options']['checked'] ?? false) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="{{ $field['name'] }}">
                            {{ $field['label'] }}
                        </label>
                        @error($field['name'])
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                @else
                    <div class="mb-3">
                        <label for="{{ $field['name'] }}" class="form-label">
                            {{ $field['label'] }}
                            @if($field['options']['required'] ?? false)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <input
                            type="{{ $field['type'] }}"
                            class="form-control @error($field['name']) is-invalid @enderror"
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            value="{{ old($field['name']) }}"
                            placeholder="{{ $field['options']['placeholder'] ?? '' }}"
                            {{ ($field['options']['required'] ?? false) ? 'required' : '' }}
                        >
                        @error($field['name'])
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
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
