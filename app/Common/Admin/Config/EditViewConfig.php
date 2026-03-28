<?php

namespace App\Common\Admin\Config;

use App\Common\Admin\Form\FormBuilder;

class EditViewConfig
{
    private FormBuilder $formBuilder;

    private string $title = 'Editar';

    private string $submitLabel = 'Actualizar';

    private mixed $item = null;

    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function submitLabel(string $label): self
    {
        $this->submitLabel = $label;
        return $this;
    }

    public function item(mixed $item): self
    {
        $this->item = $item;
        return $this;
    }

    public function getFormBuilder(): FormBuilder
    {
        return $this->formBuilder;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubmitLabel(): string
    {
        return $this->submitLabel;
    }

    public function getItem(): mixed
    {
        return $this->item;
    }
}
