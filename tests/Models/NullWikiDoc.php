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

use Modules\Knowledgebase\Models\NullWikiDoc;

/**
 * @internal
 */
final class Null extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Knowledgebase\Models\NullWikiDoc
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Knowledgebase\Models\WikiDoc', new NullWikiDoc());
    }

    /**
     * @covers Modules\Knowledgebase\Models\NullWikiDoc
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullWikiDoc(2);
        self::assertEquals(2, $null->getId());
    }
}
