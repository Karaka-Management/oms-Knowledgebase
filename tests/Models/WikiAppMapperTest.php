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

use Modules\Knowledgebase\Models\WikiApp;
use Modules\Knowledgebase\Models\WikiAppMapper;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiAppMapperTest: Wiki application mapper
 *
 * @internal
 */
final class WikiAppMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @testdox The model can be created and read from the database
     * @covers Modules\Knowledgebase\Models\WikiAppMapper
     * @group module
     */
    public function testCR() : void
    {
        $app = new WikiApp();

        $app->name = 'Test Category';

        $id = WikiAppMapper::create()->execute($app);
        self::assertGreaterThan(0, $app->getId());
        self::assertEquals($id, $app->getId());

        $appR = WikiAppMapper::get()->where('id', $app->getId())->execute();
        self::assertEquals($app->name, $appR->name);
    }
}
