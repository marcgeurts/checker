<?php

namespace ClickNow\Checker\Context\Git;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;

/**
 * @group  context/git
 * @covers \ClickNow\Checker\Context\Git\PreCommitContext
 */
class PreCommitContextTest extends \PHPUnit_Framework_TestCase
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
     * @var \ClickNow\Checker\Context\Git\PreCommitContext
     */
    protected $preCommitContext;

    protected function setUp()
    {
        $this->runner = m::mock(RunnerInterface::class);
        $this->files = m::mock(FilesCollection::class);
        $this->preCommitContext = new PreCommitContext($this->runner, $this->files);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->preCommitContext);
    }

    public function testGetRunner()
    {
        $runner = $this->preCommitContext->getRunner();

        $this->assertInstanceOf(RunnerInterface::class, $runner);
        $this->assertSame($this->runner, $runner);
    }

    public function testGetFiles()
    {
        $files = $this->preCommitContext->getFiles();

        $this->assertInstanceOf(FilesCollection::class, $files);
        $this->assertSame($this->files, $files);
    }
}
