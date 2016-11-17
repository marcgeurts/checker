<?php

namespace ClickNow\Checker\Context;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Repository\FilesCollection;
use Mockery as m;

/**
 * @group context
 * @covers \ClickNow\Checker\Context\RunContext
 */
class RunContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\CommandInterface|\Mockery\MockInterface
     */
    protected $command;

    /**
     * @var \ClickNow\Checker\Repository\FilesCollection|\Mockery\MockInterface
     */
    protected $files;

    /**
     * @var \ClickNow\Checker\Context\RunContext
     */
    protected $runContext;

    protected function setUp()
    {
        $this->command = m::mock(CommandInterface::class);
        $this->files = m::mock(FilesCollection::class);
        $this->runContext = new RunContext($this->command, $this->files);
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
        $command = $this->runContext->getCommand();

        $this->assertInstanceOf(CommandInterface::class, $command);
        $this->assertSame($this->command, $command);
    }

    public function testGetFiles()
    {
        $files = $this->runContext->getFiles();

        $this->assertInstanceOf(FilesCollection::class, $files);
        $this->assertSame($this->files, $files);
    }
}
