<?php

namespace Tests\Unit;

use App\Common\Admin\Config\ListViewConfig;
use App\Common\Admin\Services\Filters\TextFilterStrategy;
use PHPUnit\Framework\TestCase;

class ListViewConfigTest extends TestCase
{
    public function testCanAddFilterToColumn(): void
    {
        $config = new ListViewConfig();
        $config->addColumn('name', 'Name');

        $column = $config->getColumn('name');

        $this->assertNotNull($column);
        $this->assertFalse($column->hasFilter());

        $column->setFilter(TextFilterStrategy::class);

        $this->assertTrue($column->hasFilter());
        $this->assertInstanceOf(TextFilterStrategy::class, $column->getFilter());
    }

    public function testCanFindColumnByKey(): void
    {
        $config = new ListViewConfig();
        $config->addColumn('id', 'ID');
        $config->addColumn('name', 'Name');
        $config->addColumn('email', 'Email');

        $nameColumn = $config->getColumn('name');

        $this->assertNotNull($nameColumn);
        $this->assertEquals('name', $nameColumn->getKey());
        $this->assertEquals('Name', $nameColumn->getLabel());
    }

    public function testGetColumnReturnsNullForNonExistentColumn(): void
    {
        $config = new ListViewConfig();
        $config->addColumn('name', 'Name');

        $column = $config->getColumn('nonexistent');

        $this->assertNull($column);
    }
}
