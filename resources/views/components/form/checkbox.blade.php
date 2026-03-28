@props([
    'name',
    'label',
    'checked' => false,
    'value' => '1'
])

<div class="mb-3 form-check">
    <input
        type="checkbox"
        class="form-check-input @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $attributes }}
    >
    <label class="form-check-label" for="{{ $name }}">
        {{ $label }}
    </label>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
