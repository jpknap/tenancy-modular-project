@extends('layouts.layout_menu_sidebar')

@section('title', 'Tenant List')

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 fw-bold">{{$admin->getTitle()}}</h2>
            <p class="text-muted mb-0">Administra y visualiza todos los tenants del sistema</p>
        </div>
        <button class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Nuevo Tenant
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="content-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-building text-primary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">Total Registros</h6>
                        <h3 class="mb-0 fw-bold">{{ $items->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">Activos</h6>
                        <h3 class="mb-0 fw-bold">{{ $items->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                        <i class="bi bi-clock text-warning fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">Pendientes</h6>
                        <h3 class="mb-0 fw-bold">0</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="content-card">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="bi bi-x-circle text-danger fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">Inactivos</h6>
                        <h3 class="mb-0 fw-bold">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-semibold">Listado de {{ $admin->getTitle() }}</h5>
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Buscar...">
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="fw-semibold">#</th>
                        @foreach($admin->getListableAttributes() as $attribute)
                            <th scope="col" class="fw-semibold text-capitalize">{{ ucfirst($attribute) }}</th>
                        @endforeach
                        <th scope="col" class="fw-semibold text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            @foreach($admin->getListableAttributes() as $attribute)
                                <td>
                                    <span class="fw-medium">{{ $item->$attribute ?? 'N/A' }}</span>
                                </td>
                            @endforeach
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($admin->getListableAttributes()) + 2 }}" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p class="mb-0">No hay registros disponibles</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <p class="text-muted mb-0 small">Mostrando <strong>{{ $items->count() }}</strong> resultados</p>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Anterior</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
@endsection
