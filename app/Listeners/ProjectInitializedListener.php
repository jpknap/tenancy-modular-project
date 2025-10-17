<?php

namespace App\Listeners;

use App\Services\ProjectInitService;

class ProjectInitializedListener
{
    public function __construct(
        private readonly ProjectInitService $projectInitService
    ) {
    }

    public function handle(object $event): void
    {
        $this->projectInitService->init();
    }
}
