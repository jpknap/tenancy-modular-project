<?php

namespace App\Projects\TicketsValdi\Repositories;

use App\Common\Repository\BaseRepository;
use App\Projects\TicketsValdi\Models\Institution;

class InstitutionRepository extends BaseRepository
{
    protected function model(): string
    {
        return Institution::class;
    }
}
