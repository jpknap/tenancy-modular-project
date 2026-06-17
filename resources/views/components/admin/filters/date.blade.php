@php $filterId = 'filter_' . str_replace(['.', '-'], '_', $columnName); @endphp
<input
    type="text"
    class="form-control form-control-sm column-filter-text"
    id="{{ $filterId }}"
    name="filters[{{ $columnName }}]"
    value="{{ $currentValue ?? '' }}"
    placeholder="dd/mm/yyyy"
    maxlength="10"
    data-column="{{ $columnName }}"
    data-event="input"
    data-min-length="{{ $minLength }}"
    autocomplete="off"
>
