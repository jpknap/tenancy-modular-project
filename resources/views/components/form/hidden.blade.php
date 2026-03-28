@props([
    'name',
    'value' => null
])

<input 
    type="hidden" 
    name="{{ $name }}" 
    value="{{ old($name, $value) }}"
    {{ $attributes }}
>
