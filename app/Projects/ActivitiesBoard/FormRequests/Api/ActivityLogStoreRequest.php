<?php

namespace App\Projects\ActivitiesBoard\FormRequests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            // Generado por el cliente (PWA) para permitir reintentos
            // idempotentes al sincronizar registros offline.
            'client_uuid' => ['required', 'uuid'],
            'occurred_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
