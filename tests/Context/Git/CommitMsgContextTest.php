<?php

namespace ClickNow\Checker\Context\Git;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;

/**
 * @group  context/git
 * @covers \ClickNow\Checker\Context\Git\CommitMsgContext
 */
class CommitMsgContextTest extends \PHPUnit_Framework_TestCase
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
     * @var \ClickNow\Checker\Context\Git\CommitMsgContext
     */
    protected $commitMsgContext;

    protected function setUp()
    {
        $this->runner = m::mock(RunnerInterface::class);
        $this->files = m::mock(FilesCollection::class);
        $this->commitMsgContext = new CommitMsgContext($this->runner, $this->files, 'foo', 'bar', 'foo@bar');
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->commitMsgContext);
    }

    public function testGetRunner()
    {
        $runner = $this->commitMsgContext->getRunner();

        $this->assertInstanceOf(RunnerInterface::class, $runner);
        $this->assertSame($this->runner, $runner);
    }

    public function testGetFiles()
    {
        $files = $this->commitMsgContext->getFiles();

        $this->assertInstanceOf(FilesCollection::class, $files);
        $this->assertSame($this->files, $files);
    }

    public function testGetCommitMessage()
    {
        $this->assertSame('foo', $this->commitMsgContext->getCommitMessage());
    }

    public function testGetUserName()
    {
        $this->assertSame('bar', $this->commitMsgContext->getUserName());
    }

    public function testGetUserEmail()
    {
        $this->assertSame('foo@bar', $this->commitMsgContext->getUserEmail());
    }
}
