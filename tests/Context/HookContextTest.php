<?php

namespace ClickNow\Checker\Context;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Util\FilesCollection;
use Mockery as m;

/**
 * @group context
 * @covers \ClickNow\Checker\Context\HookContext
 */
class HookContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Context\HookContext
     */
    protected $hookContext;

    protected function setUp()
    {
        $command = m::mock(CommandInterface::class);
        $files = m::mock(FilesCollection::class);

        $this->hookContext = new HookContext($command, $files);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->hookContext);
    }

    public function testGetCommand()
    {
        $this->assertInstanceOf(CommandInterface::class, $this->hookContext->getCommand());
    }

    public function testGetFiles()
    {
        $this->assertInstanceOf(FilesCollection::class, $this->hookContext->getFiles());
    }
}
