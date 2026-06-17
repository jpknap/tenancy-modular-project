@php $filterId = 'filter_' . str_replace(['.', '-'], '_', $columnName); @endphp
<input
    type="number"
    class="form-control form-control-sm column-filter-text"
    id="{{ $filterId }}"
    name="filters[{{ $columnName }}]"
    value="{{ $currentValue ?? '' }}"
    placeholder="Filtrar..."
    data-column="{{ $columnName }}"
    autocomplete="off"
>
