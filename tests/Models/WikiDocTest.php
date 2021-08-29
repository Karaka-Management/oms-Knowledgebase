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
use Modules\Knowledgebase\Models\WikiDoc;
use Modules\Knowledgebase\Models\WikiStatus;
use Modules\Tag\Models\NullTag;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiDocTest: Wiki document
 *
 * @internal
 */
class WikiDocTest extends \PHPUnit\Framework\TestCase
{
    protected WikiDoc $doc;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->doc = new WikiDoc();
    }

    /**
     * @testdox The model has the expected default values after initialization
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->doc->getId());
        self::assertNull($this->doc->app);
        self::assertEquals('', $this->doc->name);
        self::assertEquals('', $this->doc->doc);
        self::assertEquals('', $this->doc->docRaw);
        self::assertEquals(WikiStatus::ACTIVE, $this->doc->getStatus());
        self::assertNull($this->doc->category);
        self::assertEquals('en', $this->doc->getLanguage());
        self::assertEquals([], $this->doc->getTags());
    }

    /**
     * @testdox The application can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function tesAppInputOutput() : void
    {
        $this->doc->app = new NullWikiApp(2);
        self::assertEquals(2, $this->doc->app->getId());
    }

    /**
     * @testdox The name can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->doc->name = 'Test name';
        self::assertEquals('Test name', $this->doc->name);
    }

    /**
     * @testdox The content can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testDocInputOutput() : void
    {
        $this->doc->doc = 'Test content';
        self::assertEquals('Test content', $this->doc->doc);
    }

    /**
     * @testdox The raw content can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testDocRawInputOutput() : void
    {
        $this->doc->docRaw = 'Test content';
        self::assertEquals('Test content', $this->doc->docRaw);
    }

    /**
     * @testdox The status can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testStatusInputOutput() : void
    {
        $this->doc->setStatus(WikiStatus::DRAFT);
        self::assertEquals(WikiStatus::DRAFT, $this->doc->getStatus());
    }

    /**
     * @testdox The category can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testCategoryInputOutput() : void
    {
        $this->doc->category = new NullWikiCategory(3);
        self::assertEquals(3, $this->doc->category->getId());
    }

    /**
     * @testdox The language can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testLanguageInputOutput() : void
    {
        $this->doc->setLanguage('de');
        self::assertEquals('de', $this->doc->getLanguage());
    }

    /**
     * @testdox A tag can be correctly added and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testTagInputOutput() : void
    {
        $this->doc->addTag(new NullTag(5));
        self::assertEquals([new NullTag(5)], $this->doc->getTags());
    }
}
