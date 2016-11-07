<?php

namespace ClickNow\Checker\Context;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Util\FilesCollection;
use Mockery as m;

class HookContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Context\HookContext
     */
    protected $context;

    protected function setUp()
    {
        $command = m::mock(CommandInterface::class);
        $files = m::mock(FilesCollection::class);

        $this->context = new HookContext($command, $files);
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