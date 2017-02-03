<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Runner\ActionsCollection;
use Mockery as m;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group  subscriber
 * @covers \ClickNow\Checker\Subscriber\ProgressSubscriber
 */
class ProgressSubscriberTest extends \PHPUnit_Framework_TestCase
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
     * @var \ClickNow\Checker\Subscriber\ProgressSubscriber
     */
    protected $progressSubscriber;

    protected function setUp()
    {
        $this->progressBar = m::mock(ProgressBar::class);

        $this->io = m::mock(IOInterface::class);
        $this->io->shouldReceive('createProgressBar')->withAnyArgs()->once()->andReturn($this->progressBar);

        $this->progressSubscriber = new ProgressSubscriber($this->io);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->progressSubscriber);
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', ProgressSubscriber::getSubscribedEvents());
    }

    public function testStartProgress()
    {
        $actions = m::mock(ActionsCollection::class);
        $actions->shouldReceive('count')->withNoArgs()->once()->andReturn(2);

        $event = m::mock(RunnerEvent::class);
        $event->shouldReceive('getActions')->withNoArgs()->once()->andReturn($actions);

        $this->progressBar->shouldReceive('start')->with(2)->once()->andReturnNull();
        $this->progressSubscriber->startProgress($event);
    }

    public function testAdvanceProgress()
    {
        $this->progressBar->shouldReceive('advance')->withNoArgs()->once()->andReturnNull();
        $this->progressSubscriber->advanceProgress();
    }

    public function testFinishProgress()
    {
        $this->progressBar->shouldReceive('getProgress')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('getMaxSteps')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('finish')->withNoArgs()->once()->andReturnNull();
        $this->io->shouldReceive('newLine')->with(2)->once()->andReturnNull();
        $this->progressSubscriber->finishProgress();
    }

    public function testFinishProgressAborted()
    {
        $this->progressBar->shouldReceive('getProgress')->withNoArgs()->once()->andReturn(1);
        $this->progressBar->shouldReceive('getMaxSteps')->withNoArgs()->once()->andReturn(2);
        $this->progressBar->shouldReceive('finish')->withNoArgs()->never();
        $this->io->shouldReceive('newLine')->with(2)->once()->andReturnNull();
        $this->io->shouldReceive('caution')->with('Aborted...')->once()->andReturnNull();
        $this->progressSubscriber->finishProgress();
    }
}
