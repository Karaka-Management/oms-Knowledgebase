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

use Modules\Knowledgebase\Models\WikiApp;

/**
 * @testdox Modules\tests\Knowledgebase\Models\WikiAppTest: Wiki application
 *
 * @internal
 */
final class WikiAppTest extends \PHPUnit\Framework\TestCase
{
    protected WikiApp $app;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->app = new WikiApp();
    }

    /**
     * @testdox The model has the expected default values after initialization
     * @covers Modules\Knowledgebase\Models\WikiApp
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->app->getId());
        self::assertEquals('', $this->app->name);
    }

    /**
     * @testdox The name can be correctly set and returned
     * @covers Modules\Knowledgebase\Models\WikiApp
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->app->name = 'Test name';
        self::assertEquals('Test name', $this->app->name);
    }
}
