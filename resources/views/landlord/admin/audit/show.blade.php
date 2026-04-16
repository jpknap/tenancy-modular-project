@extends('layouts.layout_menu_sidebar')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Detalle de Actividad</h2>
            <p class="text-muted">Tenant: <strong>{{ $tenant->name }}</strong></p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('landlord.admin.audit.list', ['tenant_id' => $tenant->id]) }}" class="btn btn-outline-secondary">Volver</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Fecha</dt>
                <dd class="col-sm-9">{{ $audit->created_at->format('d/m/Y H:i:s') }}</dd>

                <dt class="col-sm-3">Usuario</dt>
                <dd class="col-sm-9">{{ $audit->causer?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Acción</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-{{ $audit->event === 'created' ? 'success' : ($audit->event === 'updated' ? 'info' : 'danger') }}">
                        {{ ucfirst($audit->event) }}
                    </span>
                </dd>

                <dt class="col-sm-3">Modelo</dt>
                <dd class="col-sm-9">{{ class_basename($audit->subject_type ?? '') }}</dd>

                <dt class="col-sm-3">ID Registro</dt>
                <dd class="col-sm-9">{{ $audit->subject_id ?? '—' }}</dd>

                <dt class="col-sm-3">Log Name</dt>
                <dd class="col-sm-9">{{ $audit->log_name }}</dd>
            </dl>
        </div>
    </div>

    @if ($audit->properties)
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Cambios Registrados</h5>
            </div>
            <div class="card-body">
                @if (isset($audit->properties['old']) && isset($audit->properties['new']))
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th class="text-danger">Valor Anterior</th>
                                    <th class="text-success">Valor Nuevo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($audit->properties['new'] as $field => $newValue)
                                    @php
                                        $oldValue = $audit->properties['old'][$field] ?? null;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $field }}</strong></td>
                                        <td class="text-danger">
                                            @if (is_array($oldValue) || is_object($oldValue))
                                                <code>{{ json_encode($oldValue, JSON_UNESCAPED_UNICODE) }}</code>
                                            @else
                                                {{ $oldValue ?? '—' }}
                                            @endif
                                        </td>
                                        <td class="text-success">
                                            @if (is_array($newValue) || is_object($newValue))
                                                <code>{{ json_encode($newValue, JSON_UNESCAPED_UNICODE) }}</code>
                                            @else
                                                {{ $newValue ?? '—' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Sin cambios registrados.</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
