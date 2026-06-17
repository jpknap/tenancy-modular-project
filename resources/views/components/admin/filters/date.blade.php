@php $filterId = 'filter_' . str_replace(['.', '-'], '_', $columnName); @endphp
<input
    type="date"
    class="form-control form-control-sm column-filter-text"
    id="{{ $filterId }}"
    name="filters[{{ $columnName }}]"
    value="{{ $currentValue ?? '' }}"
    data-column="{{ $columnName }}"
    autocomplete="off"
>
