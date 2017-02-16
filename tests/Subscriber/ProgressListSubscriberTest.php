<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group  subscriber
 * @covers \ClickNow\Checker\Subscriber\ProgressListSubscriber
 */
class ProgressListSubscriberTest extends \PHPUnit_Framework_TestCase
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
     * @var \ClickNow\Checker\Subscriber\ProgressListSubscriber
     */
    protected $progressListSubscriber;

    protected function setUp()
    {
        $this->progressBar = m::mock(ProgressBar::class);

        $this->io = m::mock(IOInterface::class);
        $this->io->shouldReceive('createProgressBar')->withAnyArgs()->once()->andReturn($this->progressBar);

        $this->progressListSubscriber = new ProgressListSubscriber($this->io);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->progressListSubscriber);
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', ProgressListSubscriber::getSubscribedEvents());
        $this->assertCount(6, ProgressListSubscriber::getSubscribedEvents());
    }

    public function testStartProgressDisabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner(false));
        $runnerEvent->shouldReceive('getActions->count')->withNoArgs()->never();

        $this->progressBar->shouldReceive('start')->withAnyArgs()->never();

        $this->progressListSubscriber->startProgress($runnerEvent);
    }

    public function testStartProgressEnabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());
        $runnerEvent->shouldReceive('getActions->count')->withNoArgs()->once()->andReturn(3);

        $this->progressBar->shouldReceive('setFormat')->with('/Running/')->once()->andReturnNull();
        $this->progressBar->shouldReceive('start')->with(3)->once()->andReturnNull();

        $this->progressListSubscriber->startProgress($runnerEvent);
    }

    public function testAdvanceProgressDisabled()
    {
        $actionEvent = m::mock(ActionEvent::class);
        $actionEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner(false));
        $actionEvent->shouldReceive('getResult')->withNoArgs()->never();
        $actionEvent->shouldReceive('getAction')->withNoArgs()->never();

        $this->progressListSubscriber->advanceProgress($actionEvent);
    }

    public function testAdvanceProgressEnabledInProgress()
    {
        $actionEvent = m::mock(ActionEvent::class);
        $actionEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());
        $actionEvent->shouldReceive('getResult')->withNoArgs()->once()->andReturnNull();
        $actionEvent->shouldReceive('getAction->getName')->withNoArgs()->once()->andReturn('ACTION');

        $this->progressBar->shouldReceive('setMessage')->with('/ACTION/')->once()->andReturnNull();
        $this->progressBar->shouldReceive('setMessage')->with('/In progress/', 'status')->once()->andReturnNull();
        $this->progressBar->shouldReceive('advance')->withNoArgs()->once()->andReturnNull();

        $this->progressListSubscriber->advanceProgress($actionEvent);
    }

    /**
     * @dataProvider actionStatus
     */
    public function testAdvanceProgressEnabled($status, $message)
    {
        $result = m::mock(ResultInterface::class);
        $result->shouldReceive('getStatus')->withNoArgs()->once()->andReturn($status);

        $actionEvent = m::mock(ActionEvent::class);
        $actionEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());
        $actionEvent->shouldReceive('getResult')->withNoArgs()->once()->andReturn($result);
        $actionEvent->shouldReceive('getAction->getName')->withNoArgs()->once()->andReturn('ACTION');

        $this->progressBar->shouldReceive('setMessage')->with('/ACTION/')->once()->andReturnNull();
        $this->progressBar->shouldReceive('setOverwrite')->with(true)->once()->andReturnNull();
        $this->progressBar->shouldReceive('setMessage')->with($message, 'status')->once()->andReturnNull();
        $this->progressBar->shouldReceive('display')->withNoArgs()->once()->andReturnNull();
        $this->progressBar->shouldReceive('setOverwrite')->with(false)->once()->andReturnNull();

        $this->progressListSubscriber->advanceProgress($actionEvent);
    }

    public function actionStatus()
    {
        return [
            [ResultInterface::SUCCESS, '<fg=green>Ok</fg=green>'],
            [ResultInterface::WARNING, '<fg=yellow>Warning</fg=yellow>'],
            [ResultInterface::ERROR, '<fg=red>Error</fg=red>'],
            [ResultInterface::SKIPPED, '<fg=cyan>Skipped</fg=cyan>'],
        ];
    }

    public function testFinishProgressDisabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner(false));

        $this->progressBar->shouldReceive('getProgress')->withNoArgs()->never();
        $this->progressBar->shouldReceive('getMaxSteps')->withNoArgs()->never();
        $this->progressBar->shouldReceive('finish')->withNoArgs()->never();

        $this->io->shouldReceive('newLine')->with(2)->never();

        $this->progressListSubscriber->finishProgress($runnerEvent);
    }

    public function testFinishProgressEnabled()
    {
        $runnerEvent = m::mock(RunnerEvent::class);
        $runnerEvent->shouldReceive('getContext->getRunner')->withNoArgs()->once()->andReturn($this->mockRunner());

        $this->progressBar->shouldReceive('getProgress')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('getMaxSteps')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('finish')->withNoArgs()->once()->andReturnNull();

        $this->io->shouldReceive('newLine')->with(2)->once()->andReturnNull();

        $this->progressListSubscriber->finishProgress($runnerEvent);
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

        $this->progressListSubscriber->finishProgress($runnerEvent);
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
        $runner->shouldReceive('getProgress')->withNoArgs()->once()->andReturn(($enabled) ? 'list' : null);

        return $runner;
    }
}
