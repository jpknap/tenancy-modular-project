<?php

namespace App\Common\Audit\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    /**
     * Get the database connection for the model.
     *
     * @return string|null
     */
    public function getConnectionName()
    {
        // Use the current connection when in a tenant context
        // or the default connection for the landlord
        return null;
    }
}
