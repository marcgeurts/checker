<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\IO\IOInterface;
use Mockery as m;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder as SymfonyProcessBuilder;

/**
 * @group process
 * @covers \ClickNow\Checker\Process\ProcessBuilder
 */
class ProcessBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Process\ExecutableFinder|\Mockery\MockInterface
     */
    protected $executableFinder;

    /**
     * @var \Symfony\Component\Process\ProcessBuilder|\Mockery\MockInterface
     */
    protected $symfonyProcessBuilder;

    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \ClickNow\Checker\Process\ProcessBuilder
     */
    protected $processBuilder;

    protected function setUp()
    {
        $this->executableFinder = m::mock(ExecutableFinder::class);
        $this->symfonyProcessBuilder = m::mock(SymfonyProcessBuilder::class);
        $this->io = m::mock(IOInterface::class);
        $this->processBuilder = new ProcessBuilder($this->executableFinder, $this->symfonyProcessBuilder, $this->io);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testCreateArgumentsForCommand()
    {
        $this->executableFinder->shouldReceive('find')->with('foo')->once()->andReturn('bin/foo');

        $result = $this->processBuilder->createArgumentsForCommand('foo');

        $this->assertInstanceOf(ArgumentsCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame('bin/foo', $result->first());
    }

    public function testBuildProcess()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->atMost()->once()->andReturnNull();
        $process->shouldReceive('getCommandLine')->withNoArgs()->once()->andReturn('bin/foo');

        $this->symfonyProcessBuilder->shouldReceive('setArguments')->with(['bin/foo'])->once()->andReturnSelf();
        $this->symfonyProcessBuilder->shouldReceive('setTimeout')->with(null)->once()->andReturnSelf();
        $this->symfonyProcessBuilder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        $this->io->shouldReceive('log')->with('Command: bin/foo')->once()->andReturnNull();

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getProcessTimeout')->withNoArgs()->once()->andReturnNull();

        $arguments = m::mock(ArgumentsCollection::class);
        $arguments->shouldReceive('getValues')->withNoArgs()->once()->andReturn(['bin/foo']);

        $result = $this->processBuilder->buildProcess($arguments, $command);

        $this->assertInstanceOf(Process::class, $result);
        $this->assertSame($process, $result);
    }
}
