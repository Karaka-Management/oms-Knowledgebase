<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\tests\Models;

use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiCategory;
use phpOMS\Localization\BaseStringL11n;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Knowledgebase\Models\WikiCategory::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\tests\Knowledgebase\Models\WikiCateboryTest: Wiki category')]
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

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The model has the expected default values after initialization')]
    public function testDefault() : void
    {
        self::assertEquals(0, $this->category->id);
        self::assertEquals(0, $this->category->app->id);
        self::assertEquals('', $this->category->getL11n());
        self::assertEquals('/', $this->category->virtualPath);
        self::assertEquals(0, $this->category->parent->id);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The application can correctly set and returned')]
    public function testAppInputOutput() : void
    {
        $this->category->app = new NullWikiApp(2);
        self::assertEquals(2, $this->category->app->id);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The name can correctly set and returned')]
    public function testNameInputOutput() : void
    {
        $this->category->setL11n('Test');
        self::assertEquals('Test', $this->category->getL11n());

        $this->category->setL11n(new BaseStringL11n('NewTest'));
        self::assertEquals('NewTest', $this->category->getL11n());
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The path can correctly set and returned')]
    public function testPathInputOutput() : void
    {
        $this->category->virtualPath = '/test/path';
        self::assertEquals('/test/path', $this->category->virtualPath);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The parent can correctly set and returned')]
    public function testParentInputOutput() : void
    {
        $this->category->parent = new NullWikiCategory(2);
        self::assertEquals(2, $this->category->parent->id);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testSerialize() : void
    {
        $this->category->app         = new NullWikiApp(1);
        $this->category->virtualPath = '/test/path';

        $serialized = $this->category->jsonSerialize();

        self::assertEquals(
            [
                'id'          => 0,
                'app'         => $this->category->app,
                'virtualPath' => '/test/path',
            ],
            $serialized
        );
    }
}
