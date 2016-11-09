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
    protected function tearDown()
    {
        m::close();
    }

    public function testCreateArgumentsForCommand()
    {
        $executableFinder = m::mock(ExecutableFinder::class);
        $executableFinder->shouldReceive('find')->with('foo')->once()->andReturn('bin/foo');

        $symfonyProcessBuilder = m::mock(SymfonyProcessBuilder::class);
        $io = m::mock(IOInterface::class);

        $processBuilder = new ProcessBuilder($executableFinder, $symfonyProcessBuilder, $io);
        $result = $processBuilder->createArgumentsForCommand('foo');

        $this->assertInstanceOf(ArgumentsCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame('bin/foo', $result->first());
    }

    public function testBuildProcess()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->atMost()->once()->andReturnNull();
        $process->shouldReceive('getCommandLine')->withNoArgs()->once()->andReturn('bin/foo');

        $symfonyProcessBuilder = m::mock(SymfonyProcessBuilder::class);
        $symfonyProcessBuilder->shouldReceive('setArguments')->with(['bin/foo'])->once()->andReturnSelf();
        $symfonyProcessBuilder->shouldReceive('setTimeout')->with(null)->once()->andReturnSelf();
        $symfonyProcessBuilder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('log')->with('Command: bin/foo')->once()->andReturnNull();

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getProcessTimeout')->withNoArgs()->once()->andReturnNull();

        $processBuilder = new ProcessBuilder(m::mock(ExecutableFinder::class), $symfonyProcessBuilder, $io);
        $result = $processBuilder->buildProcess(ArgumentsCollection::forExecutable('bin/foo'), $command);

        $this->assertInstanceOf(Process::class, $result);
        $this->assertSame($process, $result);
    }
}
