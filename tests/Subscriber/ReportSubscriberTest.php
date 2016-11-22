<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Console\Helper\PathsHelper;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultsCollection;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group subscriber
 * @covers \ClickNow\Checker\Subscriber\ReportSubscriber
 */
class ReportSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \ClickNow\Checker\Console\Helper\PathsHelper|\Mockery\MockInterface
     */
    protected $paths;

    /**
     * @var \ClickNow\Checker\Subscriber\ReportSubscriber
     */
    protected $reportSubscriber;

    protected function setUp()
    {
        $this->io = m::mock(IOInterface::class);
        $this->paths = m::mock(PathsHelper::class);
        $this->reportSubscriber = new ReportSubscriber($this->io, $this->paths);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->reportSubscriber);
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', ReportSubscriber::getSubscribedEvents());
        $this->assertCount(2, ReportSubscriber::getSubscribedEvents());
    }

    public function testOnReportNull()
    {
        $event = m::mock(RunnerEvent::class);
        $event->shouldReceive('getResults')->withNoArgs()->once()->andReturnNull();
        $event->shouldReceive('getContext')->withNoArgs()->never();

        $this->reportSubscriber->onReport($event);
    }

    public function testOnReportEmpty()
    {
        $results = new ResultsCollection();

        $event = m::mock(RunnerEvent::class);
        $event->shouldReceive('getResults')->withNoArgs()->once()->andReturn($results);
        $event->shouldReceive('getContext')->withNoArgs()->never();

        $this->reportSubscriber->onReport($event);
    }

    public function testOnReportSuccessWithMessage()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(false);
        $command->shouldReceive('getMessage')->with('successfully')->once()->andReturn('successfully');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));

        $this->paths->shouldReceive('getMessage')->with('successfully')->once()->andReturn('successfully');
        $this->io->shouldReceive('text')->with('<fg=green>successfully</fg=green>')->once()->andReturnNull();

        $this->reportSubscriber->onReport($this->mockEvent($command, $results));
    }

    public function testOnReportSuccessWithoutMessage()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(false);
        $command->shouldReceive('getMessage')->with('successfully')->once()->andReturnNull();

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();
        $this->io->shouldReceive('text')->withAnyArgs()->never();

        $this->reportSubscriber->onReport($this->mockEvent($command, $results));
    }

    public function testOnReportSuccessAndWarning()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(false);
        $command->shouldReceive('getMessage')->with('successfully')->once()->andReturnNull();

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::WARNING, 'WARNING1'));
        $results->add($this->mockResult(Result::WARNING, 'WARNING2'));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();
        $this->io->shouldReceive('note')->with('WARNING1')->once()->andReturnNull()->ordered();
        $this->io->shouldReceive('note')->with('WARNING2')->once()->andReturnNull()->ordered();

        $this->reportSubscriber->onReport($this->mockEvent($command, $results));
    }

    public function testOnReportWithSkippedSuccess()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(true);

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::WARNING, 'WARNING1'));
        $results->add($this->mockResult(Result::WARNING, 'WARNING2'));

        $this->paths->shouldReceive('getMessage')->withAnyArgs()->never();
        $this->io->shouldReceive('note')->with('WARNING1')->once()->andReturnNull()->ordered();
        $this->io->shouldReceive('note')->with('WARNING2')->once()->andReturnNull()->ordered();

        $this->reportSubscriber->onReport($this->mockEvent($command, $results));
    }

    public function testOnReportErrorWithMessage()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('isSkipSuccessOutput')->withNoArgs()->never();
        $command->shouldReceive('getMessage')->with('failed')->once()->andReturn('failed');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::ERROR, 'ERROR1'));
        $results->add($this->mockResult(Result::ERROR, 'ERROR2'));

        $this->paths->shouldReceive('getMessage')->with('failed')->once()->andReturn('failed');
        $this->io->shouldReceive('text')->with('<fg=red>failed</fg=red>')->once()->andReturnNull();
        $this->io->shouldReceive('error')->with('ERROR1')->once()->andReturnNull()->ordered();
        $this->io->shouldReceive('error')->with('ERROR2')->once()->andReturnNull()->ordered();

        $this->reportSubscriber->onReport($this->mockEvent($command, $results));
    }

    public function testOnReportErrorWithoutMessage()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('isSkipSuccessOutput')->withNoArgs()->never();
        $command->shouldReceive('getMessage')->with('failed')->once()->andReturnNull();

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::ERROR, 'ERROR1'));
        $results->add($this->mockResult(Result::ERROR, 'ERROR2'));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();
        $this->io->shouldReceive('text')->withAnyArgs()->never();
        $this->io->shouldReceive('error')->with('ERROR1')->once()->andReturnNull()->ordered();
        $this->io->shouldReceive('error')->with('ERROR2')->once()->andReturnNull()->ordered();

        $this->reportSubscriber->onReport($this->mockEvent($command, $results));
    }

    public function testOnReportErrorAndWarning()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('isSkipSuccessOutput')->withNoArgs()->never();
        $command->shouldReceive('getMessage')->with('failed')->once()->andReturnNull();

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::WARNING, 'WARNING1'));
        $results->add($this->mockResult(Result::WARNING, 'WARNING2'));
        $results->add($this->mockResult(Result::ERROR, 'ERROR1'));
        $results->add($this->mockResult(Result::ERROR, 'ERROR2'));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();
        $this->io->shouldReceive('note')->with('WARNING1')->once()->andReturnNull()->ordered();
        $this->io->shouldReceive('note')->with('WARNING2')->once()->andReturnNull()->ordered();
        $this->io->shouldReceive('error')->with('ERROR1')->once()->andReturnNull()->ordered();
        $this->io->shouldReceive('error')->with('ERROR2')->once()->andReturnNull()->ordered();

        $this->reportSubscriber->onReport($this->mockEvent($command, $results));
    }

    /**
     * Mock event.
     *
     * @param \ClickNow\Checker\Result\ResultsCollection $results
     * @param \ClickNow\Checker\Command\CommandInterface $command
     *
     * @return \ClickNow\Checker\Event\RunnerEvent|\Mockery\MockInterface
     */
    protected function mockEvent(CommandInterface $command, ResultsCollection $results)
    {
        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getCommand')->withNoArgs()->once()->andReturn($command);

        $event = m::mock(RunnerEvent::class);
        $event->shouldReceive('getResults')->withNoArgs()->once()->andReturn($results);
        $event->shouldReceive('getContext')->withNoArgs()->once()->andReturn($context);

        return $event;
    }

    /**
     * Mock result.
     *
     * @param int  $status
     * @param null $message
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    protected function mockResult($status, $message = null)
    {
        $command = m::mock(CommandInterface::class);
        $context = m::mock(ContextInterface::class);
        $action = m::mock(ActionInterface::class);

        return new Result($status, $command, $context, $action, $message);
    }
}
