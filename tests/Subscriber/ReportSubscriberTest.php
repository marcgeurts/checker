<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\Helper\PathsHelper;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultsCollection;
use ClickNow\Checker\Runner\ActionInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @group  subscriber
 * @covers \ClickNow\Checker\Subscriber\ReportSubscriber
 */
class ReportSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \ClickNow\Checker\Helper\PathsHelper|\Mockery\MockInterface
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
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(false);
        $runner->shouldReceive('getMessage')->with('successfully')->once()->andReturn('successfully');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));

        $this->paths->shouldReceive('getMessage')->with('successfully')->once()->andReturn('successfully');
        $this->io->shouldReceive('successText')->with('successfully')->once()->andReturnNull();

        $this->reportSubscriber->onReport($this->mockEvent($runner, $results));
    }

    public function testOnReportSuccessWithoutMessage()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(false);
        $runner->shouldReceive('getMessage')->with('successfully')->once()->andReturnNull();

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();
        $this->io->shouldReceive('text')->withAnyArgs()->never();

        $this->reportSubscriber->onReport($this->mockEvent($runner, $results));
    }

    public function testOnReportSuccessAndWarning()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(false);
        $runner->shouldReceive('getMessage')->with('successfully')->once()->andReturnNull();

        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);

        $action1->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION1');
        $action2->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION2');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::WARNING, $action1, 'WARNING1'));
        $results->add($this->mockResult(Result::WARNING, $action2, 'WARNING2'));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();

        $this->io->shouldReceive('warning')->with('ACTION1')->once()->andReturnNull();
        $this->io->shouldReceive('warningText')->with('WARNING1')->once()->andReturnNull();
        $this->io->shouldReceive('warning')->with('ACTION2')->once()->andReturnNull();
        $this->io->shouldReceive('warningText')->with('WARNING2')->once()->andReturnNull();

        $this->reportSubscriber->onReport($this->mockEvent($runner, $results));
    }

    public function testOnReportWithSkippedSuccess()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isSkipSuccessOutput')->withNoArgs()->once()->andReturn(true);

        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);

        $action1->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION1');
        $action2->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION2');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::SUCCESS));
        $results->add($this->mockResult(Result::WARNING, $action1, 'WARNING1'));
        $results->add($this->mockResult(Result::WARNING, $action2, 'WARNING2'));

        $this->paths->shouldReceive('getMessage')->withAnyArgs()->never();

        $this->io->shouldReceive('warning')->with('ACTION1')->once()->andReturnNull();
        $this->io->shouldReceive('warningText')->with('WARNING1')->once()->andReturnNull();
        $this->io->shouldReceive('warning')->with('ACTION2')->once()->andReturnNull();
        $this->io->shouldReceive('warningText')->with('WARNING2')->once()->andReturnNull();

        $this->reportSubscriber->onReport($this->mockEvent($runner, $results));
    }

    public function testOnReportErrorWithMessage()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isSkipSuccessOutput')->withNoArgs()->never();
        $runner->shouldReceive('getMessage')->with('failed')->once()->andReturn('failed');

        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);

        $action1->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION1');
        $action2->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION2');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::ERROR, $action1, 'ERROR1'));
        $results->add($this->mockResult(Result::ERROR, $action2, 'ERROR2'));

        $this->paths->shouldReceive('getMessage')->with('failed')->once()->andReturn('failed');

        $this->io->shouldReceive('errorText')->with('failed')->once()->andReturnNull();
        $this->io->shouldReceive('error')->with('ACTION1')->once()->andReturnNull();
        $this->io->shouldReceive('errorText')->with('ERROR1')->once()->andReturnNull();
        $this->io->shouldReceive('error')->with('ACTION2')->once()->andReturnNull();
        $this->io->shouldReceive('errorText')->with('ERROR2')->once()->andReturnNull();

        $this->reportSubscriber->onReport($this->mockEvent($runner, $results));
    }

    public function testOnReportErrorWithoutMessage()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isSkipSuccessOutput')->withNoArgs()->never();
        $runner->shouldReceive('getMessage')->with('failed')->once()->andReturnNull();

        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);

        $action1->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION1');
        $action2->shouldReceive('getName')->withNoArgs()->once()->andReturn('ACTION2');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::ERROR, $action1, 'ERROR1'));
        $results->add($this->mockResult(Result::ERROR, $action2, 'ERROR2'));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();

        $this->io->shouldReceive('errorText')->with(null)->never();
        $this->io->shouldReceive('error')->with('ACTION1')->once()->andReturnNull();
        $this->io->shouldReceive('errorText')->with('ERROR1')->once()->andReturnNull();
        $this->io->shouldReceive('error')->with('ACTION2')->once()->andReturnNull();
        $this->io->shouldReceive('errorText')->with('ERROR2')->once()->andReturnNull();

        $this->reportSubscriber->onReport($this->mockEvent($runner, $results));
    }

    public function testOnReportErrorAndWarning()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isSkipSuccessOutput')->withNoArgs()->never();
        $runner->shouldReceive('getMessage')->with('failed')->once()->andReturnNull();

        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);

        $action1->shouldReceive('getName')->withNoArgs()->twice()->andReturn('ACTION1');
        $action2->shouldReceive('getName')->withNoArgs()->twice()->andReturn('ACTION2');

        $results = new ResultsCollection();
        $results->add($this->mockResult(Result::WARNING, $action1, 'WARNING1'));
        $results->add($this->mockResult(Result::WARNING, $action2, 'WARNING2'));
        $results->add($this->mockResult(Result::ERROR, $action1, 'ERROR1'));
        $results->add($this->mockResult(Result::ERROR, $action2, 'ERROR2'));

        $this->paths->shouldReceive('getMessage')->with(null)->once()->andReturnNull();

        $this->io->shouldReceive('warning')->with('ACTION1')->once()->andReturnNull();
        $this->io->shouldReceive('warningText')->with('WARNING1')->once()->andReturnNull();
        $this->io->shouldReceive('warning')->with('ACTION2')->once()->andReturnNull();
        $this->io->shouldReceive('warningText')->with('WARNING2')->once()->andReturnNull();
        $this->io->shouldReceive('error')->with('ACTION1')->once()->andReturnNull();
        $this->io->shouldReceive('errorText')->with('ERROR1')->once()->andReturnNull();
        $this->io->shouldReceive('error')->with('ACTION2')->once()->andReturnNull();
        $this->io->shouldReceive('errorText')->with('ERROR2')->once()->andReturnNull();

        $this->reportSubscriber->onReport($this->mockEvent($runner, $results));
    }

    /**
     * Mock event.
     *
     * @param \ClickNow\Checker\Result\ResultsCollection $results
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     *
     * @return \ClickNow\Checker\Event\RunnerEvent|\Mockery\MockInterface
     */
    protected function mockEvent(RunnerInterface $runner, ResultsCollection $results)
    {
        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getRunner')->withNoArgs()->once()->andReturn($runner);

        $event = m::mock(RunnerEvent::class);
        $event->shouldReceive('getResults')->withNoArgs()->once()->andReturn($results);
        $event->shouldReceive('getContext')->withNoArgs()->once()->andReturn($context);

        return $event;
    }

    /**
     * Mock result.
     *
     * @param int                                      $status
     * @param \ClickNow\Checker\Runner\ActionInterface $action
     * @param null                                     $message
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    protected function mockResult($status, ActionInterface $action = null, $message = null)
    {
        $runner = m::mock(RunnerInterface::class);
        $context = m::mock(ContextInterface::class);

        return new Result($status, $runner, $context, $action ?: m::mock(ActionInterface::class), $message);
    }
}
