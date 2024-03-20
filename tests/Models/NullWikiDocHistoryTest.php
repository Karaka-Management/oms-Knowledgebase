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

use Modules\Knowledgebase\Models\NullWikiDocHistory;

/**
 * @internal
 */
final class NullWikiDocHistoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\Knowledgebase\Models\NullWikiDocHistory
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Knowledgebase\Models\WikiDocHistory', new NullWikiDocHistory());
    }

    /**
     * @covers \Modules\Knowledgebase\Models\NullWikiDocHistory
     * @group module
     */
    public function testId() : void
    {
        $null = new NullWikiDocHistory(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers \Modules\Knowledgebase\Models\NullWikiDocHistory
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullWikiDocHistory(2);
        self::assertEquals(['id' => 2], $null->jsonSerialize());
    }
}
