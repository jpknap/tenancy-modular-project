<?php

namespace App\Common\Admin\Form;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest base con soporte para FormBuilder
 * Patrón Template Method - Define esqueleto, subclases implementan detalles
 */
abstract class BaseFormRequest extends FormRequest
{
    protected ?FormBuilder $formBuilder = null;

    /**
     * Define la estructura del formulario
     * Hook method - Implementado por subclases
     */
    abstract public function buildForm(): FormBuilder;

    /**
     * Obtiene el FormBuilder configurado
     * Template method - Define el flujo
     */
    public function getFormBuilder(): FormBuilder
    {
        if ($this->formBuilder === null) {
            $this->formBuilder = new FormBuilder();
        }

        return $this->buildForm();
    }

    /**
     * Reglas de validación
     * Hook method - Implementado por subclases
     */
    abstract public function rules(): array;

    /**
     * Autorización por defecto
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mensajes personalizados de validación
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Atributos personalizados para mensajes
     */
    public function attributes(): array
    {
        return [];
    }
}
