<?php

namespace ClickNow\Checker\Console\Helper;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\Result;
use Mockery as m;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @group console/helper
 * @covers \ClickNow\Checker\Console\Helper\RunnerHelper
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
     * @var \ClickNow\Checker\Console\Helper\RunnerHelper
     */
    protected $runnerHelper;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \ClickNow\Checker\Command\CommandInterface|\Mockery\MockInterface
     */
    protected $command;

    /**
     * @var \ClickNow\Checker\Action\ActionsCollection
     */
    protected $actions;

    protected function setUp()
    {
        $this->dispatcher = m::mock(EventDispatcherInterface::class);
        $this->io = m::mock(IOInterface::class);
        $this->runnerHelper = new RunnerHelper($this->dispatcher, $this->io);
        $this->context = m::mock(ContextInterface::class);
        $this->command = m::mock(CommandInterface::class);
        $this->actions = new ActionsCollection();

        $this->context->shouldReceive('getCommand')->withNoArgs()->andReturn($this->command);
        $this->command->shouldReceive('getName')->withNoArgs()->andReturn('foo');
        $this->command->shouldReceive('getActionsToRun')->with($this->context)->andReturn($this->actions);
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

        $this->createAction('action1', Result::SUCCESS);
        $this->createAction('action2', Result::WARNING);
        $this->createAction('action3', Result::SKIPPED);

        $runnerType = m::type(RunnerEvent::class);

        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_RUN, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_FAILED, $runnerType)->never();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_SUCCESSFULLY, $runnerType)->once();

        $this->assertSame(0, $this->runnerHelper->run($this->context));
    }

    public function testRunAndReturnCodeError()
    {
        $this->io->shouldReceive('title')->with('/`foo`/')->atMost()->once()->andReturnNull();
        $this->command->shouldReceive('isStopOnFailure')->withNoArgs()->andReturn(false);

        $this->createAction('action1', Result::SUCCESS);
        $this->createAction('action2', Result::WARNING);
        $this->createAction('action3', Result::SKIPPED);
        $this->createAction('action4', Result::ERROR);

        $runnerType = m::type(RunnerEvent::class);

        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_RUN, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_FAILED, $runnerType)->once();
        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_SUCCESSFULLY, $runnerType)->never();

        $this->assertSame(1, $this->runnerHelper->run($this->context));
    }

    public function testRunWithStopOnFailure()
    {
        $this->io->shouldReceive('title')->with('/`foo`/')->atMost()->once()->andReturnNull();
        $this->command->shouldReceive('isStopOnFailure')->withNoArgs()->andReturn(true);

        $this->createAction('action1', Result::ERROR);
        $this->createAction('action2', Result::ERROR, 0);

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
     *
     * @return void
     */
    protected function createAction($name, $status, $times = 1)
    {
        $action = m::mock(ActionInterface::class);
        $action->shouldReceive('getName')->withNoArgs()->times($times)->andReturn($name);

        $result = new Result($status, $this->command, $this->context, $action, null);

        $this->actions->add($action);
        $this->io->shouldReceive('log')->with('/`'.$name.'`/')->times($times)->andReturnNull();
        $this->command->shouldReceive('runAction')->with($this->context, $action)->times($times)->andReturn($result);

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->with(ActionEvent::ACTION_RUN, m::type(ActionEvent::class))
            ->times($times)
            ->andReturn(m::mock(ActionEvent::class));

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->with(ActionEvent::ACTION_FAILED, m::type(ActionEvent::class))
            ->times($result->isSuccess() ? 0 : $times)
            ->andReturn(m::mock(ActionEvent::class));

        $this->dispatcher
            ->shouldReceive('dispatch')
            ->with(ActionEvent::ACTION_SUCCESSFULLY, m::type(ActionEvent::class))
            ->times($result->isSuccess() ? $times : 0)
            ->andReturn(m::mock(ActionEvent::class));
    }
}
