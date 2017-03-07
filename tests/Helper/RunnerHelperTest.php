<?php

namespace ClickNow\Checker\Helper;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\ActionInterface;
use ClickNow\Checker\Runner\ActionsCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @group  helper
 * @covers \ClickNow\Checker\Helper\RunnerHelper
 */
class RunnerHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\Mockery\MockInterface
     */
    protected $dispatcher;

    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \ClickNow\Checker\Helper\RunnerHelper
     */
    protected $runnerHelper;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface|\Mockery\MockInterface
     */
    protected $runner;

    /**
     * @var \ClickNow\Checker\Runner\ActionsCollection
     */
    protected $actions;

    protected function setUp()
    {
        $this->context = m::mock(ContextInterface::class);
        $this->runner = m::mock(RunnerInterface::class);
        $this->actions = new ActionsCollection();

        $this->dispatcher = m::mock(EventDispatcherInterface::class);
        $this->io = m::mock(IOInterface::class);
        $this->runnerHelper = new RunnerHelper($this->dispatcher, $this->io);

        $this->context->shouldReceive('getRunner')->withNoArgs()->andReturn($this->runner);
        $this->runner->shouldReceive('getName')->withNoArgs()->andReturn('foo');
        $this->runner->shouldReceive('getActionsToRun')->with($this->context)->andReturn($this->actions);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(HelperInterface::class, $this->runnerHelper);
        $this->assertInstanceOf(Helper::class, $this->runnerHelper);
    }

    public function testGetName()
    {
        $this->assertSame('runner', $this->runnerHelper->getName());
    }

    public function testRunWithoutActions()
    {
        $this->runner->shouldReceive('isSkipEmptyOutput')->twice()->withNoArgs()->andReturn(false);

        $this->io->shouldReceive('title')->with('/`foo`/')->atMost()->once()->andReturnNull();
        $this->io->shouldReceive('note')->with('No actions available.')->once()->andReturnNull();

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->with(RunnerEvent::RUNNER_RUN, m::type(RunnerEvent::class))
            ->never();

        $this->assertSame(0, $this->runnerHelper->run($this->context));
    }

    public function testRunAndReturnCodeSuccess()
    {
        $this->io->shouldReceive('title')->with('/`foo`/')->atMost()->once()->andReturnNull();
        $this->runner->shouldReceive('isStrict')->withNoArgs()->twice()->andReturn(false);

        $this->createAction('action1', ResultInterface::SUCCESS);
        $this->createAction('action2', ResultInterface::WARNING);
        $this->createAction('action3', ResultInterface::SKIPPED);

        $runnerType = m::type(RunnerEvent::class);

        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_RUN, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_FAILED, $runnerType)->never();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_SUCCESSFULLY, $runnerType)->once();

        $this->assertSame(0, $this->runnerHelper->run($this->context));
    }

    public function testRunAndReturnCodeError()
    {
        $this->io->shouldReceive('title')->with('/`foo`/')->atMost()->once()->andReturnNull();
        $this->runner->shouldReceive('isStopOnFailure')->withNoArgs()->andReturn(false);
        $this->runner->shouldReceive('isStrict')->withNoArgs()->twice()->andReturn(false);

        $this->createAction('action1', ResultInterface::SUCCESS);
        $this->createAction('action2', ResultInterface::WARNING);
        $this->createAction('action3', ResultInterface::SKIPPED);
        $this->createAction('action4', ResultInterface::ERROR);

        $runnerType = m::type(RunnerEvent::class);

        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_RUN, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_FAILED, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_SUCCESSFULLY, $runnerType)->never();

        $this->assertSame(1, $this->runnerHelper->run($this->context));
    }

    public function testRunAndReturnCodeErrorWithStrict()
    {
        $this->io->shouldReceive('title')->with('/`foo`/')->atMost()->once()->andReturnNull();
        $this->runner->shouldReceive('isStopOnFailure')->withNoArgs()->andReturn(true);
        $this->runner->shouldReceive('isStrict')->withNoArgs()->twice()->andReturn(true);

        $this->createAction('action1', ResultInterface::SUCCESS);
        $this->createAction('action2', ResultInterface::WARNING, 1, true);
        $this->createAction('action3', ResultInterface::SKIPPED);

        $runnerType = m::type(RunnerEvent::class);

        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_RUN, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_FAILED, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_SUCCESSFULLY, $runnerType)->never();

        $this->assertSame(1, $this->runnerHelper->run($this->context));
    }

    public function testRunWithStopOnFailure()
    {
        $this->io->shouldReceive('title')->with('/`foo`/')->atMost()->once()->andReturnNull();
        $this->runner->shouldReceive('isStopOnFailure')->withNoArgs()->andReturn(true);
        $this->runner->shouldReceive('isStrict')->withNoArgs()->once()->andReturn(false);

        $this->createAction('action1', ResultInterface::ERROR);
        $this->createAction('action2', ResultInterface::ERROR, 0);

        $runnerType = m::type(RunnerEvent::class);

        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_RUN, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_FAILED, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_SUCCESSFULLY, $runnerType)->never();

        $this->assertSame(1, $this->runnerHelper->run($this->context));
    }

    /**
     * Create action.
     *
     * @param string $name
     * @param int    $status
     * @param int    $times
     * @param bool   $strict
     *
     * @return void
     */
    protected function createAction($name, $status, $times = 1, $strict = false)
    {
        $action = m::mock(ActionInterface::class);
        $action->shouldReceive('getName')->withNoArgs()->times($times)->andReturn($name);

        $result = new Result($status, $this->runner, $this->context, $action, null);

        $this->actions->add($action);
        $this->io->shouldReceive('log')->with('/`'.$name.'`/')->times($times)->andReturnNull();
        $this->runner->shouldReceive('runAction')->with($this->context, $action)->times($times)->andReturn($result);

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->with(ActionEvent::ACTION_RUN, m::type(ActionEvent::class))
            ->times($times)
            ->andReturn(m::mock(ActionEvent::class));

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->with(ActionEvent::ACTION_FAILED, m::type(ActionEvent::class))
            ->times($result->isError() || ($result->isWarning() && $strict) ? $times : 0)
            ->andReturn(m::mock(ActionEvent::class));

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->with(ActionEvent::ACTION_SUCCESSFULLY, m::type(ActionEvent::class))
            ->times($result->isError() || ($result->isWarning() && $strict) ? 0 : $times)
            ->andReturn(m::mock(ActionEvent::class));
    }
}
