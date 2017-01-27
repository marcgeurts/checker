<?php

namespace ClickNow\Checker\Context\Git;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;

/**
 * @group  context/git
 * @covers \ClickNow\Checker\Context\Git\PrePushContext
 */
class PrePushContextTest extends \PHPUnit_Framework_TestCase
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
     * @var \ClickNow\Checker\Context\Git\PrePushContext
     */
    protected $prePushContext;

    protected function setUp()
    {
        $this->runner = m::mock(RunnerInterface::class);
        $this->files = m::mock(FilesCollection::class);
        $this->prePushContext = new PrePushContext($this->runner, $this->files);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->prePushContext);
    }

    public function testGetRunner()
    {
        $runner = $this->prePushContext->getRunner();

        $this->assertInstanceOf(RunnerInterface::class, $runner);
        $this->assertSame($this->runner, $runner);
    }

    public function testGetFiles()
    {
        $files = $this->prePushContext->getFiles();

        $this->assertInstanceOf(FilesCollection::class, $files);
        $this->assertSame($this->files, $files);
    }
}
