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
     * @covers Modules\Knowledgebase\Models\WikiDocMapper
     * @group module
     */
    public function testCR() : void
    {
        $doc = new WikiDoc();

        $doc->name = 'Doc Name';
        $doc->doc  = 'Doc content';
        $doc->setStatus(WikiStatus::DRAFT);
        $doc->category = new NullWikiCategory(1);
        $doc->setLanguage('en');
        $doc->createdBy = new NullAccount(1);

        $id = WikiDocMapper::create()->execute($doc);
        self::assertGreaterThan(0, $doc->getId());
        self::assertEquals($id, $doc->getId());

        $docR = WikiDocMapper::get()->where('id', $doc->getId())->execute();
        self::assertEquals($doc->name, $docR->name);
        self::assertEquals($doc->doc, $docR->doc);
        self::assertEquals($doc->getStatus(), $docR->getStatus());
        self::assertEquals($doc->getLanguage(), $docR->getLanguage());
        self::assertEquals($doc->category->getId(), $docR->category->getId());

        self::assertGreaterThan(0,
            \count(
                WikiDocMapper::getAll()->where('app', 1)->sort('id', OrderType::DESC)->execute()
            )
        );
    }
}
