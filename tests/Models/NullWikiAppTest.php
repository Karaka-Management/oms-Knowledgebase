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
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\tests\Models;

use Modules\Knowledgebase\Models\NullWikiApp;

/**
 * @internal
 */
final class NullWikiAppTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Knowledgebase\Models\NullWikiApp
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Knowledgebase\Models\WikiApp', new NullWikiApp());
    }

    /**
     * @covers Modules\Knowledgebase\Models\NullWikiApp
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullWikiApp(2);
        self::assertEquals(2, $null->getId());
    }
}
