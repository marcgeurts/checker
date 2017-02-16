<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group  subscriber
 * @covers \ClickNow\Checker\Subscriber\ProgressBarSubscriber
 */
class ProgressBarSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar|\Mockery\MockInterface
     */
    protected $progressBar;

    /**
     * @var \ClickNow\Checker\Subscriber\ProgressBarSubscriber
     */
    protected $progressBarSubscriber;

    protected function setUp()
    {
        $this->progressBar = m::mock(ProgressBar::class);

        $this->io = m::mock(IOInterface::class);
        $this->io->shouldReceive('createProgressBar')->withAnyArgs()->once()->andReturn($this->progressBar);

        $this->progressBarSubscriber = new ProgressBarSubscriber($this->io);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->progressBarSubscriber);
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', ProgressBarSubscriber::getSubscribedEvents());
        $this->assertCount(5, ProgressBarSubscriber::getSubscribedEvents());
    }

    public function testStartProgressDisabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner(false));
        $runnerEvent->shouldReceive('getActions->count')->withNoArgs()->never();

        $this->progressBar->shouldReceive('start')->withAnyArgs()->never();

        $this->progressBarSubscriber->startProgress($runnerEvent);
    }

    public function testStartProgressEnabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());
        $runnerEvent->shouldReceive('getActions->count')->withNoArgs()->once()->andReturn(3);

        $this->progressBar->shouldReceive('start')->with(3)->once()->andReturnNull();

        $this->progressBarSubscriber->startProgress($runnerEvent);
    }

    public function testAdvanceProgressDisabled()
    {
        $actionEvent = m::mock(ActionEvent::class);
        $actionEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner(false));

        $this->progressBar->shouldReceive('advance')->withNoArgs()->never();

        $this->progressBarSubscriber->advanceProgress($actionEvent);
    }

    public function testAdvanceProgressEnabled()
    {
        $actionEvent = m::mock(ActionEvent::class);
        $actionEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());

        $this->progressBar->shouldReceive('advance')->withNoArgs()->once()->andReturnNull();

        $this->progressBarSubscriber->advanceProgress($actionEvent);
    }

    public function testFinishProgressDisabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner(false));

        $this->progressBar->shouldReceive('getProgress')->withNoArgs()->never();
        $this->progressBar->shouldReceive('getMaxSteps')->withNoArgs()->never();
        $this->progressBar->shouldReceive('finish')->withNoArgs()->never();

        $this->io->shouldReceive('newLine')->with(2)->never();

        $this->progressBarSubscriber->finishProgress($runnerEvent);
    }

    public function testFinishProgressEnabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());

        $this->progressBar->shouldReceive('getProgress')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('getMaxSteps')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('finish')->withNoArgs()->once()->andReturnNull();

        $this->io->shouldReceive('newLine')->with(2)->once()->andReturnNull();

        $this->progressBarSubscriber->finishProgress($runnerEvent);
    }

    public function testFinishProgressAborted()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());

        $this->progressBar->shouldReceive('getProgress')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('getMaxSteps')->withNoArgs()->once()->andReturn(2);
        $this->progressBar->shouldReceive('finish')->withNoArgs()->never();

        $this->io->shouldReceive('newLine')->with(2)->once()->andReturnNull();
        $this->io->shouldReceive('caution')->with('Aborted...')->once()->andReturnNull();

        $this->progressBarSubscriber->finishProgress($runnerEvent);
    }

    /**
     * Mock runner.
     *
     * @param bool $enabled
     *
     * @return \ClickNow\Checker\Event\RunnerEvent|\Mockery\MockInterface
     */
    protected function mockRunner($enabled = true)
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getProgress')->withNoArgs()->once()->andReturn(($enabled) ? 'bar' : null);

        return $runner;
    }
}
