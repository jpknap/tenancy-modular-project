@extends('layouts.layout_menu_sidebar')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Registro de Actividad Cross-Tenant</h2>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET" action="{{ route('landlord.admin.audit.list') }}">
                <div class="col-md-6">
                    <label for="tenant_id" class="form-label">Seleccionar Tenant</label>
                    <select class="form-select" id="tenant_id" name="tenant_id" onchange="this.form.submit()">
                        <option value="">-- Elegir tenant --</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}" @selected($selectedTenant?->id === $tenant->id)>
                                {{ $tenant->name }} ({{ $tenant->identifier }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedTenant)
        @if ($audits->isEmpty())
            <div class="alert alert-info">
                No hay registros de actividad para este tenant.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Modelo</th>
                            <th>ID</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($audits as $audit)
                            <tr>
                                <td>{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $audit->causer?->name ?? '—' }}</td>
                                <td>{{ ucfirst($audit->event) }}</td>
                                <td>{{ class_basename($audit->subject_type ?? '') }}</td>
                                <td>{{ $audit->subject_id ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('landlord.admin.audit.show', [$selectedTenant->id, $audit->id]) }}" class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $audits->links() }}
        @endif
    @endif
</div>
@endsection
