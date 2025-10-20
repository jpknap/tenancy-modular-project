<?php

namespace App\Common\Admin\Config;


use App\Common\Form\FormBuilder;

class CreateViewConfig
{
    private FormBuilder $formBuilder;
    private string $title = 'Crear Nuevo';
    private string $submitLabel = 'Guardar';

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

}
