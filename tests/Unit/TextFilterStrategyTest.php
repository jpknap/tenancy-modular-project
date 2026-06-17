<?php

namespace Tests\Unit;

use App\Common\Admin\Services\Filters\BooleanFilterStrategy;
use App\Common\Admin\Services\Filters\DateFilterStrategy;
use App\Common\Admin\Services\Filters\NumberFilterStrategy;
use App\Common\Admin\Services\Filters\TextFilterStrategy;
use PHPUnit\Framework\TestCase;

class TextFilterStrategyTest extends TestCase
{
    public function testTextFilterStrategyReturnsCorrectType(): void
    {
        $this->assertSame('text', (new TextFilterStrategy())->getType());
    }

    public function testNumberFilterStrategyReturnsCorrectType(): void
    {
        $this->assertSame('number', (new NumberFilterStrategy())->getType());
    }

    public function testDateFilterStrategyReturnsCorrectType(): void
    {
        $this->assertSame('date', (new DateFilterStrategy())->getType());
    }

    public function testBooleanFilterStrategyReturnsCorrectType(): void
    {
        $this->assertSame('boolean', (new BooleanFilterStrategy())->getType());
    }
}
