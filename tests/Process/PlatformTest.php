<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Exception\PlatformException;
use Mockery as m;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder as SymfonyProcessBuilder;

/**
 * @group  process
 * @covers \ClickNow\Checker\Process\Platform
 */
class PlatformTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Process\ProcessBuilder|\Mockery\MockInterface
     */
    protected $builder;

    /**
     * @var \ClickNow\Checker\Process\Platform|\Mockery\MockInterface
     */
    protected $platform;

    protected function setUp()
    {
        $this->builder = m::mock(SymfonyProcessBuilder::class);
        $this->platform = new Platform($this->builder);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testValidCommandLine()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('enableOutput')->withNoArgs()->once()->andReturnSelf();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(false);
        $process->shouldReceive('getOutput')->withNoArgs()->never();

        $this->builder->shouldReceive('setArguments')->with(['getconf', 'ARG_MAX'])->once()->andReturnSelf();
        $this->builder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        $this->platform->validateCommandLineMaxLength('foo');
    }

    public function testInvalidCommandLine()
    {
        $this->setExpectedException(PlatformException::class);

        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('enableOutput')->withNoArgs()->once()->andReturnSelf();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(true);
        $process->shouldReceive('getOutput')->withNoArgs()->once()->andReturn(2);

        $this->builder->shouldReceive('setArguments')->with(['getconf', 'ARG_MAX'])->once()->andReturnSelf();
        $this->builder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        $this->platform->validateCommandLineMaxLength('foo');
    }
}
