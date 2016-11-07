<?php

namespace ClickNow\Checker\Formatter;

use Mockery as m;
use Symfony\Component\Process\Process;

class RawProcessFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Formatter\RawProcessFormatter
     */
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new RawProcessFormatter();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ProcessFormatterInterface::class, $this->formatter);
    }

    public function testDisplayTheFullProcessOutput()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->andReturnNull();
        $process->shouldReceive('getOutput')->once()->andReturn('stdout');
        $process->shouldReceive('getErrorOutput')->once()->andReturn('stderr');

        $this->assertEquals('stdout'.PHP_EOL.'stderr', $this->formatter->format($process));
    }

    public function testDisplayStdoutOnly()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->andReturnNull();
        $process->shouldReceive('getOutput')->once()->andReturn('stdout');
        $process->shouldReceive('getErrorOutput')->once()->andReturn('');

        $this->assertEquals('stdout', $this->formatter->format($process));
    }

    public function testDisplayStderrOnly()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->andReturnNull();
        $process->shouldReceive('getOutput')->once()->andReturn('');
        $process->shouldReceive('getErrorOutput')->once()->andReturn('stderr');

        $this->assertEquals('stderr', $this->formatter->format($process));
    }
}