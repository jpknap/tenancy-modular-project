<?php

namespace App\Projects\ActivitiesBoard\Repositories;

use App\Common\Repository\BaseRepository;
use App\Projects\ActivitiesBoard\Models\ActivityLog;

class ActivityLogRepository extends BaseRepository
{
    protected function model(): string
    {
        return ActivityLog::class;
    }
}
