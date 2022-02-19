<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Knowledgebase\tests\Models;

use Modules\Knowledgebase\Models\NullWikiCategory;

/**
 * @internal
 */
final class NullWikiCategoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Knowledgebase\Models\NullWikiCategory
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Knowledgebase\Models\WikiCategory', new NullWikiCategory());
    }

    /**
     * @covers Modules\Knowledgebase\Models\NullWikiCategory
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullWikiCategory(2);
        self::assertEquals(2, $null->getId());
    }
}
