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
    protected $rawProcessFormatter;

    protected function setUp()
    {
        $this->rawProcessFormatter = new RawProcessFormatter();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ProcessFormatterInterface::class, $this->rawProcessFormatter);
    }

    public function testDisplayTheFullProcessOutput()
    {
        $result = $this->rawProcessFormatter->format($this->mockProcess());
        $this->assertSame('stdout'.PHP_EOL.'stderr', $result);
    }

    public function testDisplayStdoutOnly()
    {
        $process = $this->mockProcess();
        $process->shouldReceive('getErrorOutput')->withNoArgs()->once()->andReturnNull();

        $result = $this->rawProcessFormatter->format($process);
        $this->assertSame('stdout', $result);
    }

    public function testDisplayStderrOnly()
    {
        $process = $this->mockProcess();
        $process->shouldReceive('getOutput')->withNoArgs()->once()->andReturnNull();

        $result = $this->rawProcessFormatter->format($process);
        $this->assertSame('stderr', $result);
    }

    /**
     * Mock process.
     *
     * @return \Symfony\Component\Process\Process|\Mockery\MockInterface
     */
    protected function mockProcess()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->atMost()->once()->andReturnNull();
        $process->shouldReceive('getOutput')->withNoArgs()->once()->andReturn('stdout')->byDefault();
        $process->shouldReceive('getErrorOutput')->withNoArgs()->once()->andReturn('stderr')->byDefault();

        return $process;
    }
}
