<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Command\CommandInterface;
use Mockery as m;
use Symfony\Component\Process\Process;

/**
 * @group  process
 * @covers \ClickNow\Checker\Process\AsyncProcessRunner
 */
class AsyncProcessRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Process\AsyncProcessRunner
     */
    protected $asyncProcessRunner;

    protected function setUp()
    {
        $this->asyncProcessRunner = new AsyncProcessRunner();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getProcessAsyncWait')->withNoArgs()->atLeast()->once()->andReturn(0);
        $command->shouldReceive('getProcessAsyncLimit')->withNoArgs()->atLeast()->once()->andReturn(5);

        for ($i = 0; $i < 20; $i++) {
            $process = $this->prophesize(Process::class);

            $process->started = false;
            $process->terminated = false;

            $process->start()->will(function () use ($process) {
                $process->started = true;
            })->shouldBeCalledTimes(1);

            $process->isTerminated()->will(function () use ($process) {
                if (!$process->terminated) {
                    $process->terminated = true;

                    return false;
                }

                return true;
            })->shouldBeCalledTimes(2);

            // The number of times isStarted() is called starts at 3
            // and increases by 2 after each chunk of five processes.
            $process->isStarted()->will(function () use ($process) {
                return $process->started;
            })->shouldBeCalledTimes(floor($i / 5) * 2 + 3);

            $this->asyncProcessRunner->add($process->reveal());
        }

        $this->asyncProcessRunner->run($command);
    }
}
