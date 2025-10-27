@props(['field'])

@php
    $type = $field['type'] ?? 'text';
    $name = $field['name'];
    $label = $field['label'] ?? '';
    $options = $field['options'] ?? [];
    $value = $field['value'] ?? null;
@endphp

@switch($type)
    @case('hidden')
        <x-form.hidden 
            :name="$name" 
            :value="$value" 
        />
        @break

    @case('textarea')
        <x-form.textarea
            :name="$name"
            :label="$label"
            :placeholder="$options['placeholder'] ?? ''"
            :required="$options['required'] ?? false"
            :rows="$options['rows'] ?? 3"
            :value="$value"
        />
        @break

    @case('select')
        <x-form.select
            :name="$name"
            :label="$label"
            :options="$field['choices'] ?? []"
            :required="$options['required'] ?? false"
            :value="$value"
        />
        @break

    @case('checkbox')
        <x-form.checkbox
            :name="$name"
            :label="$label"
            :checked="$options['checked'] ?? false"
            :value="$value ?? '1'"
        />
        @break

    @default
        <x-form.input
            :name="$name"
            :label="$label"
            :type="$type"
            :placeholder="$options['placeholder'] ?? ''"
            :required="$options['required'] ?? false"
            :value="$value"
        />
@endswitch
