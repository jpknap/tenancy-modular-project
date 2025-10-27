<?php

namespace App\Common\Admin\Enum;

enum FormContextEnum: string
{
    case CREATE = 'create';
    case EDIT = 'edit';

    public function label(): string
    {
        return match ($this) {
            self::CREATE => 'Crear',
            self::EDIT => 'Editar',
        };
    }

    public function isCreate(): bool
    {
        return $this === self::CREATE;
    }

    public function isEdit(): bool
    {
        return $this === self::EDIT;
    }
}
