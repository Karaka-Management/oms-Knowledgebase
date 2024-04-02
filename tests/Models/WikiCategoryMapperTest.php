<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use phpOMS\Localization\ISO639x1Enum;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Knowledgebase\Models\WikiCategoryMapper::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\tests\Knowledgebase\Models\WikiCategoryMapperTest: Wiki category mapper')]
final class WikiCategoryMapperTest extends \PHPUnit\Framework\TestCase
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
    #[\PHPUnit\Framework\Attributes\TestDox('The model can be created and read from the database')]
    public function testCR() : void
    {
        $this->category->setL11n('Test Category');

        $id = WikiCategoryMapper::create()->execute($this->category);
        self::assertGreaterThan(0, $this->category->id);
        self::assertEquals($id, $this->category->id);

        $categoryR = WikiCategoryMapper::get()->with('name')->where('id', $this->category->id)->where('name/language', ISO639x1Enum::_EN)->execute();
        self::assertEquals($this->category->getL11n(), $categoryR->getL11n());

        self::assertGreaterThan(0, \count(WikiCategoryMapper::getAll()->where('app', 1)->executeGetArray()));
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\TestDox('The model can be created and read from the database with a parent category')]
    public function testChildCR() : void
    {
        $this->category->app = new NullWikiApp(1);
        $this->category->setL11n('Test Category2');
        $this->category->parent = new NullWikiCategory(1);

        $id = WikiCategoryMapper::create()->execute($this->category);
        self::assertGreaterThan(0, $this->category->id);
        self::assertEquals($id, $this->category->id);

        $categoryR = WikiCategoryMapper::get()
            ->with('name')
            ->where('id', $this->category->id)
            ->where('name/language', ISO639x1Enum::_EN)
            ->execute();

        self::assertEquals($this->category->getL11n(), $categoryR->getL11n());
        self::assertEquals($this->category->parent->id, $categoryR->parent->id);

        self::assertGreaterThan(0,
            \count(
                WikiCategoryMapper::getAll()
                    ->with('name')
                    ->where('parent', 1)
                    ->where('app', 1)
                    ->where('name/language', ISO639x1Enum::_EN)
                    ->executeGetArray()
            )
        );
    }
}
