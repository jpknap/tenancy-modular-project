<?php

namespace App\Common\Services;

class LocaleService
{
    public const SUPPORTED = ['es', 'en', 'pt'];

    public static function options(): array
    {
        return [
            'es' => 'Español',
            'en' => 'English',
            'pt' => 'Português',
        ];
    }
}
