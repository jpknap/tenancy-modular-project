@php $filterId = 'filter_' . str_replace(['.', '-'], '_', $columnName); @endphp
<select
    class="form-select form-select-sm column-filter-text"
    id="{{ $filterId }}"
    name="filters[{{ $columnName }}]"
    data-column="{{ $columnName }}"
>
    <option value="">— Todos —</option>
    <option value="1" @selected(($currentValue ?? '') === '1')>Sí</option>
    <option value="0" @selected(($currentValue ?? '') === '0')>No</option>
</select>
