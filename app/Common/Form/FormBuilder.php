<?php

namespace App\Common\Form;

/**
 * Constructor de formularios con sintaxis fluida
 * Patrón Builder para construcción paso a paso
 */
class FormBuilder
{
    private array $fields = [];

    private string $method = 'POST';

    private string $action = '';

    private array $attributes = [];

    public function setMethod(string $method): self
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function text(string $name, string $label, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'text',
            'name' => $name,
            'label' => $label,
            'options' => $options,
        ];
        return $this;
    }

    public function email(string $name, string $label, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'email',
            'name' => $name,
            'label' => $label,
            'options' => $options,
        ];
        return $this;
    }

    public function password(string $name, string $label, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'password',
            'name' => $name,
            'label' => $label,
            'options' => $options,
        ];
        return $this;
    }

    public function textarea(string $name, string $label, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'textarea',
            'name' => $name,
            'label' => $label,
            'options' => $options,
        ];
        return $this;
    }

    public function select(string $name, string $label, array $choices, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'select',
            'name' => $name,
            'label' => $label,
            'choices' => $choices,
            'options' => $options,
        ];
        return $this;
    }

    public function checkbox(string $name, string $label, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'checkbox',
            'name' => $name,
            'label' => $label,
            'options' => $options,
        ];
        return $this;
    }

    public function date(string $name, string $label, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'date',
            'name' => $name,
            'label' => $label,
            'options' => $options,
        ];
        return $this;
    }

    public function number(string $name, string $label, array $options = []): self
    {
        $this->fields[] = [
            'type' => 'number',
            'name' => $name,
            'label' => $label,
            'options' => $options,
        ];
        return $this;
    }

    public function hidden(string $name, mixed $value = null): self
    {
        $this->fields[] = [
            'type' => 'hidden',
            'name' => $name,
            'value' => $value,
        ];
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'action' => $this->action,
            'attributes' => $this->attributes,
            'fields' => $this->fields,
        ];
    }
}
