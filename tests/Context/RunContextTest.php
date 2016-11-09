<?php

namespace ClickNow\Checker\Context;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Util\FilesCollection;
use Mockery as m;

/**
 * @group context
 * @covers \ClickNow\Checker\Context\RunContext
 */
class RunContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Context\RunContext
     */
    protected $runContext;

    protected function setUp()
    {
        $command = m::mock(CommandInterface::class);
        $files = m::mock(FilesCollection::class);

        $this->runContext = new RunContext($command, $files);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->runContext);
    }

    public function testGetCommand()
    {
        $this->assertInstanceOf(CommandInterface::class, $this->runContext->getCommand());
    }

    public function testGetFiles()
    {
        $this->assertInstanceOf(FilesCollection::class, $this->runContext->getFiles());
    }
}
