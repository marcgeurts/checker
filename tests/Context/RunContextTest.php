<?php

namespace ClickNow\Checker\Context;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;

/**
 * @group  context
 * @covers \ClickNow\Checker\Context\RunContext
 */
class RunContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface|\Mockery\MockInterface
     */
    protected $runner;

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
        $this->runner = m::mock(RunnerInterface::class);
        $this->files = m::mock(FilesCollection::class);
        $this->runContext = new RunContext($this->runner, $this->files);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->runContext);
    }

    public function testGetRunner()
    {
        $runner = $this->runContext->getRunner();

        $this->assertInstanceOf(RunnerInterface::class, $runner);
        $this->assertSame($this->runner, $runner);
    }

    public function testGetFiles()
    {
        $files = $this->runContext->getFiles();

        $this->assertInstanceOf(FilesCollection::class, $files);
        $this->assertSame($this->files, $files);
    }
}
