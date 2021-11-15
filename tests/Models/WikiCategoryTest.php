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
use Modules\Knowledgebase\Models\WikiCategoryL11n;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiCateboryTest: Wiki category
 *
 * @internal
 */
final class WikiCategoryTest extends \PHPUnit\Framework\TestCase
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
     * @covers Modules\Knowledgebase\Models\WikiCategory
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->category->getId());
        self::assertEquals(0, $this->category->app->getId());
        self::assertEquals('', $this->category->getL11n());
        self::assertEquals('/', $this->category->virtualPath);
        self::assertEquals(0, $this->category->parent->getId());
    }

    /**
     * @testdox The application can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiCategory
     * @group module
     */
    public function testAppInputOutput() : void
    {
        $this->category->app = new NullWikiApp(2);
        self::assertEquals(2, $this->category->app->getId());
    }

    /**
     * @testdox The name can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiCategory
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->category->setL11n('Test');
        self::assertEquals('Test', $this->category->getL11n());

        $this->category->setL11n(new WikiCategoryL11n('NewTest'));
        self::assertEquals('NewTest', $this->category->getL11n());
    }

    /**
     * @testdox The path can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiCategory
     * @group module
     */
    public function testPathInputOutput() : void
    {
        $this->category->virtualPath = '/test/path';
        self::assertEquals('/test/path', $this->category->virtualPath);
    }

    /**
     * @testdox The parent can correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiCategory
     * @group module
     */
    public function testParentInputOutput() : void
    {
        $this->category->parent = new NullWikiCategory(2);
        self::assertEquals(2, $this->category->parent->getId());
    }

    /**
     * @covers Modules\Knowledgebase\Models\WikiCategory
     * @group module
     */
    public function testSerialize() : void
    {
        $this->category->app         = new NullWikiApp(1);
        $this->category->virtualPath = '/test/path';

        $serialized = $this->category->jsonSerialize();

        self::assertEquals(
            [
                'id'                => 0,
                'app'               => $this->category->app,
                'virtualPath'       => '/test/path',
            ],
            $serialized
        );
    }
}
