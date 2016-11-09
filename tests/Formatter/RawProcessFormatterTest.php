<?php

namespace ClickNow\Checker\Formatter;

use Mockery as m;
use Symfony\Component\Process\Process;

/**
 * @group formatter
 * @covers \ClickNow\Checker\Formatter\RawProcessFormatter
 */
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
        $process = $this->mockProcess();
        $this->assertEquals('stdout'.PHP_EOL.'stderr', $this->formatter->format($process));
    }

    public function testDisplayStdoutOnly()
    {
        $process = $this->mockProcess();
        $process->shouldReceive('getErrorOutput')->withNoArgs()->once()->andReturnNull();

        $this->assertEquals('stdout', $this->formatter->format($process));
    }

    public function testDisplayStderrOnly()
    {
        $process = $this->mockProcess();
        $process->shouldReceive('getOutput')->withNoArgs()->once()->andReturnNull();

        $this->assertEquals('stderr', $this->formatter->format($process));
    }

    /**
     * @return \Symfony\Component\Process\Process|\Mockery\MockInterface
     */
    protected function mockProcess()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->with(0)->atMost()->once()->andReturnNull();
        $process->shouldReceive('getOutput')->withNoArgs()->once()->andReturn('stdout')->byDefault();
        $process->shouldReceive('getErrorOutput')->withNoArgs()->once()->andReturn('stderr')->byDefault();

        return $process;
    }
}
