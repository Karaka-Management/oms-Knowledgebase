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
use Modules\Knowledgebase\Models\WikiDoc;
use Modules\Knowledgebase\Models\WikiStatus;
use phpOMS\Localization\ISO639x1Enum;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiDocTest: Wiki document
 *
 * @internal
 */
final class WikiDocTest extends \PHPUnit\Framework\TestCase
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
        self::assertEquals(0, $this->doc->id);
        self::assertNull($this->doc->app);
        self::assertEquals('', $this->doc->name);
        self::assertEquals('', $this->doc->doc);
        self::assertEquals('', $this->doc->docRaw);
        self::assertEquals(WikiStatus::ACTIVE, $this->doc->status);
        self::assertNull($this->doc->category);
        self::assertEquals('en', $this->doc->language);
        self::assertEquals([], $this->doc->tags);
    }

    /**
     * @testdox The application can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function tesAppInputOutput() : void
    {
        $this->doc->app = new NullWikiApp(2);
        self::assertEquals(2, $this->doc->app->id);
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
     * @testdox The category can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testCategoryInputOutput() : void
    {
        $this->doc->category = new NullWikiCategory(3);
        self::assertEquals(3, $this->doc->category->id);
    }

    /**
     * @covers Modules\Knowledgebase\Models\WikiDoc
     * @group module
     */
    public function testSerialize() : void
    {
        $this->doc->app    = new NullWikiApp(1);
        $this->doc->name   = '/test/path';
        $this->doc->status = WikiStatus::DRAFT;
        $this->doc->doc    = 'TestDoc';
        $this->doc->docRaw = 'TestDocRaw';

        $serialized = $this->doc->jsonSerialize();

        self::assertEquals(
            [
                'id'        => 0,
                'app'       => $this->doc->app,
                'name'      => '/test/path',
                'status'    => WikiStatus::DRAFT,
                'doc'       => 'TestDoc',
                'docRaw'    => 'TestDocRaw',
                'language'  => ISO639x1Enum::_EN,
                'tags'      => [],
                'media'     => [],
                'createdAt' => $this->doc->createdAt,
                'createdBy' => $this->doc->createdBy,
            ],
            $serialized
        );
    }
}
