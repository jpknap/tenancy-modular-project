@extends('layouts.layout_menu_sidebar')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Registro de Actividad</h2>
        </div>
    </div>

    @if ($audits->isEmpty())
        <div class="alert alert-info">
            No hay registros de actividad disponibles.
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
                                <a href="{{ route('sport-competition.admin.audit.show', $audit->id) }}" class="btn btn-sm btn-outline-primary">
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
</div>
@endsection
