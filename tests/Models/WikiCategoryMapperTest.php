<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\tests\Models;

use Modules\Knowledgebase\Models\NullWikiApp;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiCategory;
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use phpOMS\Localization\ISO639x1Enum;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiCategoryMapperTest: Wiki category mapper
 *
 * @internal
 */
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

    /**
     * @testdox The model can be created and read from the database
     * @covers Modules\Knowledgebase\Models\WikiCategoryMapper
     * @group module
     */
    public function testCR() : void
    {
        $this->category->setL11n('Test Category');

        $id = WikiCategoryMapper::create()->execute($this->category);
        self::assertGreaterThan(0, $this->category->getId());
        self::assertEquals($id, $this->category->getId());

        $categoryR = WikiCategoryMapper::get()->with('name')->where('id', $this->category->getId())->where('name/language', ISO639x1Enum::_EN)->execute();
        self::assertEquals($this->category->getL11n(), $categoryR->getL11n());

        self::assertGreaterThan(0, \count(WikiCategoryMapper::getAll()->where('app', 1)->execute()));
    }

    /**
     * @testdox The model can be created and read from the database with a parent category
     * @covers Modules\Knowledgebase\Models\WikiCategoryMapper
     * @group module
     */
    public function testChildCR() : void
    {
        $this->category->app = new NullWikiApp(1);
        $this->category->setL11n('Test Category2');
        $this->category->parent = new NullWikiCategory(1);

        $id = WikiCategoryMapper::create()->execute($this->category);
        self::assertGreaterThan(0, $this->category->getId());
        self::assertEquals($id, $this->category->getId());

        $categoryR = WikiCategoryMapper::get()
            ->with('name')
            ->where('id', $this->category->getId())
            ->where('name/language', ISO639x1Enum::_EN)
            ->execute();

        self::assertEquals($this->category->getL11n(), $categoryR->getL11n());
        self::assertEquals($this->category->parent->getId(), $categoryR->parent->getId());

        self::assertGreaterThan(0,
            \count(
                WikiCategoryMapper::getAll()
                    ->with('name')
                    ->where('parent', 1)
                    ->where('app', 1)
                    ->where('name/language', ISO639x1Enum::_EN)
                    ->execute()
            )
        );
    }
}
