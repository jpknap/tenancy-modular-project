<?php

namespace Tests;

use App\Common\Admin\Services\Filters\SqliteTextFilterStrategy;
use App\Common\Admin\Services\Filters\TextFilterStrategy;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(TextFilterStrategy::class, SqliteTextFilterStrategy::class);
    }
}
