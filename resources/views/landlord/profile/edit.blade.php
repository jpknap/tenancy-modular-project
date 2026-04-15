@extends('layouts.layout_menu_sidebar')

@section('title', __('profile.title'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 fw-bold">{{ __('profile.title') }}</h2>
            <p class="text-muted mb-0">{{ __('profile.subtitle') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>{{ __('profile.errors_title') }}</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="content-card">
        <form method="POST" action="{{ route(\App\Projects\Landlord\Enums\Routes::ProfileEdit->value) }}">
            @csrf
            @method('PUT')

            @foreach($form->getFields() as $field)
                @php
                    $field['value'] = old($field['name'], data_get($user, $field['name']));
                @endphp
                <x-form.field :field="$field" />
            @endforeach

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>{{ __('profile.save') }}
                </button>
            </div>
        </form>
    </div>
@endsection
