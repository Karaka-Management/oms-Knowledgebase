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

use Modules\Admin\Models\NullAccount;
use Modules\Knowledgebase\Models\NullWikiCategory;
use Modules\Knowledgebase\Models\WikiDoc;
use Modules\Knowledgebase\Models\WikiDocMapper;
use Modules\Knowledgebase\Models\WikiStatus;
use phpOMS\DataStorage\Database\Query\OrderType;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiDocMapperTest: Wiki document mapper
 *
 * @internal
 */
final class WikiDocMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The model can be created and read from the database
     * @covers \Modules\Knowledgebase\Models\WikiDocMapper
     * @group module
     */
    public function testCR() : void
    {
        $doc = new WikiDoc();

        $doc->name      = 'Doc Name';
        $doc->doc       = 'Doc content';
        $doc->status    = WikiStatus::DRAFT;
        $doc->category  = new NullWikiCategory(1);
        $doc->language  = 'en';
        $doc->createdBy = new NullAccount(1);

        $id = WikiDocMapper::create()->execute($doc);
        self::assertGreaterThan(0, $doc->id);
        self::assertEquals($id, $doc->id);

        $docR = WikiDocMapper::get()->where('id', $doc->id)->execute();
        self::assertEquals($doc->name, $docR->name);
        self::assertEquals($doc->doc, $docR->doc);
        self::assertEquals($doc->status, $docR->status);
        self::assertEquals($doc->language, $docR->language);
        self::assertEquals($doc->category->id, $docR->category->id);

        self::assertGreaterThan(0,
            \count(
                WikiDocMapper::getAll()->where('app', 1)->sort('id', OrderType::DESC)->execute()
            )
        );
    }
}
