@extends('layouts.layout_menu_sidebar')

@section('title', $config->getTitle())

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-7">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                {{-- Header --}}
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle me-3" 
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-semibold text-dark">{{ $config->getTitle() }}</h4>
                            <p class="mb-0 text-muted small">Acción irreversible</p>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 pb-4">
                    {{-- Mensaje de advertencia --}}
                    <div class="alert alert-light border-start border-4 border-warning bg-warning bg-opacity-10 mb-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-info-circle text-warning me-3 mt-1 fs-5"></i>
                            <div>
                                <p class="mb-0 text-dark">{{ $config->getMessage() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Información del registro --}}
                    @if(count($config->getDisplayFields()) > 0)
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                Información del registro
                            </h6>
                            <div class="bg-light rounded p-3">
                                @foreach($config->getDisplayFields() as $field => $label)
                                    <div class="row mb-2 {{ !$loop->last ? 'border-bottom pb-2' : '' }}">
                                        <div class="col-5 col-md-4">
                                            <span class="text-muted small">{{ $label }}</span>
                                        </div>
                                        <div class="col-7 col-md-8">
                                            <span class="text-dark fw-medium">{{ data_get($config->getItem(), $field) ?? '-' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Botones de acción --}}
                    <form method="POST" action="{{ $admin->getUrl('delete', ['id' => $config->getItem()->id]) }}">
                        @csrf
                        @method('DELETE')

                        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ $admin->getUrl('list') }}" 
                               class="btn btn-light border px-4 order-2 order-sm-1">
                                {{ $config->getCancelLabel() }}
                            </a>
                            <button type="submit" 
                                    class="btn btn-danger px-4 order-1 order-sm-2"
                                    onclick="return confirm('¿Confirma que desea eliminar este registro?');">
                                <i class="bi bi-trash3 me-1"></i>
                                {{ $config->getSubmitLabel() }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .alert-light {
        background-color: transparent;
    }
    
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        transition: all 0.2s ease;
    }
    
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
    }
    
    .btn-light {
        transition: all 0.2s ease;
    }
    
    .btn-light:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection
