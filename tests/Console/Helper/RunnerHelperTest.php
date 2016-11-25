<?php

namespace ClickNow\Checker\Console\Helper;

use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use Mockery as m;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\EventDispatcher\Event;
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

    protected function setUp()
    {
        $this->dispatcher = m::mock(EventDispatcherInterface::class);
        $this->io = m::mock(IOInterface::class);
        $this->runnerHelper = new RunnerHelper($this->dispatcher, $this->io);
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
        $context = m::mock(ContextInterface::class);
        $command = m::mock(CommandInterface::class);

        $context->shouldReceive('getCommand')->withNoArgs()->once()->andReturn($command);
        $command->shouldReceive('getName')->withNoArgs()->once()->andReturn('foo');
        $command->shouldReceive('getActionsToRun')->with($context)->once()->andReturn(new ActionsCollection());

        $this->dispatcher->shouldReceive('dispatch')->with(RunnerEvent::RUNNER_RUN, m::type(Event::class))->never();

        $this->io->shouldReceive('title')->with('/`foo`/')->once()->andReturnNull();
        $this->io->shouldReceive('note')->with('No actions available.')->once()->andReturnNull();

        $result = $this->runnerHelper->run($context);
        $this->assertSame(0, $result);
    }
}
