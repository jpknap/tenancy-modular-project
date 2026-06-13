<?php

use App\ProjectManager;
use Carbon\Carbon;

if (! function_exists('p__')) {
    /**
     * Traduce una clave usando el namespace del proyecto activo.
     * Uso: p__('messages.tenant.created')  →  __('landlord::messages.tenant.created')
     */
    function p__(string $key, array $replace = [], ?string $locale = null): string
    {
        $project = ProjectManager::getCurrentProject();
        $namespace = $project ? $project::getPrefix() : 'common';

        return __("{$namespace}::{$key}", $replace, $locale);
    }
}

if (! function_exists('display_date')) {
    /**
     * Formatea una fecha aplicando la zona horaria del usuario activo.
     * La BD siempre almacena UTC — esta función solo transforma la salida.
     */
    function display_date(Carbon|\DateTimeInterface|string $date, string $format = 'd/m/Y H:i'): string
    {
        $carbon = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);

        return $carbon->setTimezone(resolve_display_timezone())
            ->format($format);
    }
}

if (! function_exists('resolve_display_timezone')) {
    /**
     * Resuelve la zona horaria para presentación: usuario → tenant → config.
     * Nunca modifica date_default_timezone_set() — solo se usa en display.
     */
    function resolve_display_timezone(): string
    {
        $user = collect(array_keys(config('auth.guards', [])))
            ->map(fn ($guard) => auth()->guard($guard)->user())
            ->filter()
            ->first();

        if ($user?->timezone) {
            return $user->timezone;
        }

        try {
            $tenant = tenancy()->tenant;
            if ($tenant?->timezone) {
                return $tenant->timezone;
            }
        } catch (\Throwable) {
            // Contexto Landlord: no hay tenant inicializado
        }

        return config('app.timezone', 'UTC');
    }
}

if (! function_exists('resolve_tenant_timezone')) {
    /**
     * Retorna la zona horaria del tenant activo, sin considerar el usuario.
     * Usar para poblar valores por defecto en formularios de otros usuarios.
     */
    function resolve_tenant_timezone(): string
    {
        try {
            $tenant = tenancy()
                ->tenant;
            if ($tenant?->timezone) {
                return $tenant->timezone;
            }
        } catch (\Throwable) {
            // Contexto Landlord: no hay tenant inicializado
        }

        return config('app.timezone', 'UTC');
    }
}

if (! function_exists('timezone_options')) {
    /**
     * Retorna las zonas horarias disponibles agrupadas por región,
     * para usar como opciones en un select.
     *
     * @return array<string, string>
     */
    function timezone_options(bool $withBlank = false): array
    {
        $options = $withBlank ? ['' => '— Predeterminado —'] : [];

        $grouped = collect(timezone_identifiers_list())
            ->groupBy(fn ($tz) => str_contains($tz, '/') ? explode('/', $tz)[0] : 'Otros');

        foreach ($grouped->sortKeys() as $region => $zones) {
            foreach ($zones->sort() as $tz) {
                $options[$tz] = str_replace('_', ' ', $tz);
            }
        }

        return $options;
    }
}
