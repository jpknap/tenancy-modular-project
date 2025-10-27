<?php

namespace App\Common\Admin\Config;

class DeleteViewConfig
{
    private string $title = 'Eliminar Registro';
    private string $message = '¿Está seguro que desea eliminar este registro?';
    private string $submitLabel = 'Eliminar';
    private string $cancelLabel = 'Cancelar';
    private mixed $item = null;
    private array $displayFields = [];

    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function submitLabel(string $label): self
    {
        $this->submitLabel = $label;
        return $this;
    }

    public function cancelLabel(string $label): self
    {
        $this->cancelLabel = $label;
        return $this;
    }

    public function item(mixed $item): self
    {
        $this->item = $item;
        return $this;
    }

    public function displayFields(array $fields): self
    {
        $this->displayFields = $fields;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSubmitLabel(): string
    {
        return $this->submitLabel;
    }

    public function getCancelLabel(): string
    {
        return $this->cancelLabel;
    }

    public function getItem(): mixed
    {
        return $this->item;
    }

    public function getDisplayFields(): array
    {
        return $this->displayFields;
    }
}
