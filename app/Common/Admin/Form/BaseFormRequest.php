<?php

namespace App\Common\Admin\Form;

use App\Common\Admin\Enum\FormContextEnum;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    protected ?FormBuilder $formBuilder = null;
    protected \UnitEnum $currentContext;

    public function __construct()
    {
        parent::__construct();
        $this->currentContext = FormContextEnum::CREATE;
    }

    abstract public function buildCreateForm(): FormBuilder;

    abstract public function buildEditForm(): FormBuilder;

    public function buildCustomForm(\UnitEnum $context): FormBuilder
    {
        $value = $context instanceof \BackedEnum ? $context->value : $context->name;
        throw new \BadMethodCallException("Custom form '{$value}' not implemented");
    }

    public function getFormBuilder(\UnitEnum $context = null): FormBuilder
    {
        $context = $context ?? FormContextEnum::CREATE;

        if ($this->formBuilder === null) {
            $this->formBuilder = new FormBuilder();
        }

        $this->currentContext = $context;

        $contextValue = $context instanceof \BackedEnum ? $context->value : $context->name;

        return match ($contextValue) {
            FormContextEnum::CREATE->value => $this->buildCreateForm(),
            FormContextEnum::EDIT->value => $this->buildEditForm(),
            default => $this->buildCustomForm($context),
        };
    }

    public function getContext(): \UnitEnum
    {
        return $this->currentContext;
    }

    public function isCreating(): bool
    {
        return ! $this->route('id');
    }

    public function isUpdating(): bool
    {
        return (bool) $this->route('id');
    }

    public function getModelId(): ?int
    {
        return $this->route('id');
    }

    abstract public function rules(): array;

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }
}
