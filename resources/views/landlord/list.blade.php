@extends('layouts.layout_menu_sidebar')

@section('title', $admin->getTitle())

@section('content')
    @php
        $config = $admin->getListViewConfig();
    @endphp

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-1 fw-bold">{{ $admin->getTitle() }}</h2>
            <p class="text-muted mb-0">Administra y visualiza todos los registros del sistema</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    @if($config->hasStatCards())
        <div class="row g-3 mb-4">
            @foreach($config->getStatCards() as $card)
                <div class="col-md-{{ 12 / count($config->getStatCards()) }}">
                    <div class="content-card">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle {{ $card->getColorClass() }} bg-opacity-10 p-3 me-3">
                                <i class="bi {{ $card->getIcon() }} {{ $card->getTextColorClass() }} fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 small">{{ $card->getTitle() }}</h6>
                                <h3 class="mb-0 fw-bold">{{ $card->getValue($items) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Main Content Card --}}
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-semibold">Listado de {{ $admin->getTitle() }}</h5>
            <a href="{{ $admin->getUrl('create') }}" class="btn btn-primary">
                <i class="bi bi-plus me-2"></i>Nuevo
            </a>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        @foreach($config->getColumns() as $column)
                            @if($column->isVisible())
                                <th scope="col" class="fw-semibold {{ $column->getHeaderClass() }}">
                                    {{ $column->getLabel() }}
                                    @if($column->isSortable())
                                        <i class="bi bi-arrow-down-up ms-1 text-muted small"></i>
                                    @endif
                                </th>
                            @endif
                        @endforeach
                        @if(count($config->getActions()) > 0)
                            <th scope="col" class="fw-semibold text-end">Acciones</th>
                        @endif
                    </tr>
                    @php
                        $hasAnyFilters = collect($config->getColumns())
                            ->filter(fn($col) => $col->isVisible() && $col->hasFilter())
                            ->count() > 0;
                    @endphp
                    @if($hasAnyFilters)
                        <tr class="border-top">
                            @foreach($config->getColumns() as $column)
                                @if($column->isVisible())
                                    <td class="p-2">
                                        @if($column->hasFilter())
                                            <div class="d-flex">
                                                {!! $column->getFilter()->render($column->getKey(), request()->input("filters.{$column->getKey()}")) !!}
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                            @if(count($config->getActions()) > 0)
                                <td></td>
                            @endif
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            @foreach($config->getColumns() as $column)
                                @if($column->isVisible())
                                    <td class="{{ $column->getClass() }}">
                                        {!! $column->format(data_get($item, $column->getKey())) !!}
                                    </td>
                                @endif
                            @endforeach
                            @if(count($config->getActions()) > 0)
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        @foreach($config->getActions() as $action)
                                            @php $adminUser = auth('landlord')->user() ?? auth()->user(); @endphp
                                            @if((!$action->hasPermission() || $adminUser?->can($action->getPermission())) && $action->meetsCondition($adminUser))
                                                @if($action->getType() === 'form')
                                                    <form
                                                        method="POST"
                                                        action="{{ $action->getUrl($item) }}"
                                                        style="display: inline;"
                                                        @if($action->requiresConfirmation())
                                                            onsubmit="return confirm('{{ $action->getConfirmMessage() }}')"
                                                        @endif
                                                    >
                                                        @csrf
                                                        @if($action->getFormMethod() !== 'POST')
                                                            @method($action->getFormMethod())
                                                        @endif
                                                        <button
                                                            type="submit"
                                                            class="btn btn-link p-0 text-decoration-none"
                                                            title="{{ $action->getLabel() }}"
                                                        >
                                                            <i class="{{ $action->getIcon() }} fs-5"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <a
                                                        href="{{ $action->getUrl($item) }}"
                                                        class="text-decoration-none"
                                                        title="{{ $action->getLabel() }}"
                                                        @if($action->getTarget()) target="{{ $action->getTarget() }}" @endif
                                                        @if($action->requiresConfirmation())
                                                            onclick="return confirm('{{ $action->getConfirmMessage() }}')"
                                                        @endif
                                                    >
                                                        <i class="{{ $action->getIcon() }} fs-5"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($config->getColumns()) + (count($config->getActions()) > 0 ? 1 : 0) }}" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                <p class="mb-0">{{ $config->getEmptyMessage() }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(method_exists($items, 'links'))
            <div class="d-flex justify-content-between align-items-center mt-3">
                <p class="text-muted mb-0 small">
                    Mostrando <strong>{{ $items->firstItem() ?? 0 }}</strong>
                    a <strong>{{ $items->lastItem() ?? 0 }}</strong>
                    de <strong>{{ $items->total() }}</strong> resultados
                </p>
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = document.querySelectorAll('.column-filter-text');
            let debounceTimer;

            filterInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const focusedInput = this;

                    debounceTimer = setTimeout(() => {
                        const filterValue = this.value.trim();
                        const columnName = this.dataset.column;

                        const url = new URL(window.location.href);
                        const searchParams = new URLSearchParams(url.search);

                        if (filterValue.length >= 3) {
                            searchParams.set(`filters[${columnName}]`, filterValue);
                        } else {
                            searchParams.delete(`filters[${columnName}]`);
                        }

                        url.search = searchParams.toString();

                        fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const newDoc = parser.parseFromString(html, 'text/html');

                            const newTbody = newDoc.querySelector('tbody');
                            const newPagination = newDoc.querySelector('.mt-3');
                            const currentTbody = document.querySelector('tbody');
                            const currentPagination = document.querySelector('.mt-3');

                            if (newTbody) {
                                currentTbody.innerHTML = newTbody.innerHTML;
                            }

                            if (newPagination && currentPagination) {
                                currentPagination.innerHTML = newPagination.innerHTML;
                            }

                            window.history.replaceState({}, '', url.toString());
                            focusedInput.focus();
                        });
                    }, 300);
                });
            });
        });
    </script>
@endsection
