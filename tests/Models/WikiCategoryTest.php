<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\tests\Models;

use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiCategory;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiCateboryTest: Wiki category
 *
 * @internal
 */
class WikiCategoryTest extends \PHPUnit\Framework\TestCase
{
    protected WikiCategory $category;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->category = new WikiCategory();
    }

    /**
     * @testdox The model has the expected default values after initialization
     * @covers Modules\Knowledgebase\Models\WikiApp
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->category->getId());
        self::assertEquals(0, $this->category->app->getId());
        self::assertEquals('', $this->category->getL11n());
        self::assertEquals('/', $this->category->getVirtualPath());
        self::assertEquals(0, $this->category->parent->getId());
    }

    /**
     * @testdox The application can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiApp
     * @group module
     */
    public function testAppInputOutput() : void
    {
        $this->category->app = new NullWikiApp(2);
        self::assertEquals(2, $this->category->app->getId());
    }

    /**
     * @testdox The name can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiApp
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->category->setL11n('Category Name');
        self::assertEquals('Category Name', $this->category->getL11n());
    }

    /**
     * @testdox The path can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiApp
     * @group module
     */
    public function testPathInputOutput() : void
    {
        $this->category->setVirtualPath('/test/path');
        self::assertEquals('/test/path', $this->category->getVirtualPath());
    }

    /**
     * @testdox The parent can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiApp
     * @group module
     */
    public function testParentInputOutput() : void
    {
        $this->category->parent = new NullWikiCategory(2);
        self::assertEquals(2, $this->category->parent->getId());
    }
}
