<?php

namespace App\Projects\ActivitiesBoard\Repositories;

use App\Common\Repository\BaseRepository;
use App\Projects\ActivitiesBoard\Models\Category;

class CategoryRepository extends BaseRepository
{
    protected function model(): string
    {
        return Category::class;
    }
}
