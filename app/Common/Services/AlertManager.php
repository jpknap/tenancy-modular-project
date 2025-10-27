<?php

namespace App\Common\Services;

class AlertManager
{
    private array $alerts = [];

    public function success(string $message, string $title = null): self
    {
        return $this->add('success', $message, $title);
    }

    public function error(string $message, string $title = null): self
    {
        return $this->add('error', $message, $title);
    }

    public function warning(string $message, string $title = null): self
    {
        return $this->add('warning', $message, $title);
    }

    public function info(string $message, string $title = null): self
    {
        return $this->add('info', $message, $title);
    }

    private function add(string $type, string $message, ?string $title): self
    {
        $this->alerts[] = [
            'type' => $type,
            'message' => $message,
            'title' => $title,
        ];

        session()->flash("alert_{$type}", [
            'message' => $message,
            'title' => $title,
        ]);

        return $this;
    }

    public function getAlerts(): array
    {
        return $this->alerts;
    }

    public function hasAlerts(): bool
    {
        return count($this->alerts) > 0;
    }
}
