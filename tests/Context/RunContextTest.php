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
    protected $context;

    protected function setUp()
    {
        $command = m::mock(CommandInterface::class);
        $files = m::mock(FilesCollection::class);

        $this->context = new RunContext($command, $files);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->context);
    }

    public function testGetCommand()
    {
        $this->assertInstanceOf(CommandInterface::class, $this->context->getCommand());
    }

    public function testGetFiles()
    {
        $this->assertInstanceOf(FilesCollection::class, $this->context->getFiles());
    }
}
