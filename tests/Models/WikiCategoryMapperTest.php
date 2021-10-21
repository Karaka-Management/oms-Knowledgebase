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

use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiCategory;
use Modules\Knowledgebase\Models\WikiCategoryMapper;
use phpOMS\Utils\RnG\Text;

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
     * @covers Modules\Knowledgebase\Models\WikiAppMapper
     * @group module
     */
    public function testCR() : void
    {
        $this->category->setL11n('Test Category');

        $id = WikiCategoryMapper::create($this->category);
        self::assertGreaterThan(0, $this->category->getId());
        self::assertEquals($id, $this->category->getId());

        $categoryR = WikiCategoryMapper::get($this->category->getId());
        self::assertEquals($this->category->getL11n(), $categoryR->getL11n());
    }

    /**
     * @testdox The model can be created and read from the database with a parent category
     * @covers Modules\Knowledgebase\Models\WikiAppMapper
     * @group module
     */
    public function testChildCR() : void
    {
        $this->category->setL11n('Test Category2');
        $this->category->parent = new NullWikiCategory(1);

        $id = WikiCategoryMapper::create($this->category);
        self::assertGreaterThan(0, $this->category->getId());
        self::assertEquals($id, $this->category->getId());

        $categoryR = WikiCategoryMapper::get($this->category->getId());
        self::assertEquals($this->category->getL11n(), $categoryR->getL11n());
        self::assertEquals($this->category->parent->getId(), $categoryR->parent->getId());
    }

    /**
     * @group volume
     * @group module
     * @coversNothing
     */
    public function testVolume() : void
    {
        for ($i = 1; $i < 30; ++$i) {
            $text     = new Text();
            $category = new WikiCategory();

            $category->setL11n($text->generateText(\mt_rand(1, 3)));

            $id = WikiCategoryMapper::create($category);
        }
    }
}
