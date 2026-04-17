<?php

namespace Tests\Unit;

use App\Common\Admin\Services\Filters\TextFilterStrategy;
use PHPUnit\Framework\TestCase;

class TextFilterStrategyTest extends TestCase
{
    public function testFilterRenderReturnsInputWithCorrectAttributes(): void
    {
        $strategy = new TextFilterStrategy();
        $html = $strategy->render('name', 'test value');

        $this->assertStringContainsString('class="form-control form-control-sm column-filter-text"', $html);
        $this->assertStringContainsString('data-column="name"', $html);
        $this->assertStringContainsString('value="test value"', $html);
        $this->assertStringContainsString('name="filters[name]"', $html);
    }

    public function testFilterRenderEscapesSpecialCharacters(): void
    {
        $strategy = new TextFilterStrategy();
        $html = $strategy->render('name', '<script>alert("xss")</script>');

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function testFilterRenderHandlesAccents(): void
    {
        $strategy = new TextFilterStrategy();
        $html = $strategy->render('name', 'José María');

        $this->assertStringContainsString('value="José María"', $html);
    }
}
