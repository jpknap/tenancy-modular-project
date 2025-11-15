<?php

namespace App\Projects\ActivitiesBoard\Repositories;

use App\Common\Repository\BaseRepository;
use App\Projects\ActivitiesBoard\Models\Activity;

class ActivityRepository extends BaseRepository
{
    protected function model(): string
    {
        return Activity::class;
    }
}
