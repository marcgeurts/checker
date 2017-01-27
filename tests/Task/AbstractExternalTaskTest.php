<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Formatter\ProcessFormatterInterface;
use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Process\ProcessBuilder;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\AbstractExternalTask
 */
class AbstractExternalTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Process\ProcessBuilder|\Mockery\MockInterface
     */
    protected $processBuilder;

    /**
     * @var \ClickNow\Checker\Formatter\ProcessFormatterInterface|\Mockery\MockInterface
     */
    protected $processFormatter;

    /**
     * @var \ClickNow\Checker\Task\AbstractExternalTask|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $externalTask;

    protected function setUp()
    {
        $this->processBuilder = m::mock(ProcessBuilder::class);
        $this->processFormatter = m::mock(ProcessFormatterInterface::class);
        $this->externalTask = $this->getMockForAbstractClass(AbstractExternalTask::class, [
            $this->processBuilder,
            $this->processFormatter,
        ]);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(TaskInterface::class, $this->externalTask);
        $this->assertInstanceOf(AbstractTask::class, $this->externalTask);
    }

    public function testRunAndReturnSuccess()
    {
        $files = new FilesCollection([new SplFileInfo('file.php', null, null)]);
        $arguments = m::mock(ArgumentsCollection::class);

        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getActionConfig')->with($this->externalTask)->once()->andReturn([]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn($files);

        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(true);

        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('foo')->once()->andReturn($arguments);
        $this->processBuilder->shouldReceive('buildProcess')->with($arguments, $runner)->once()->andReturn($process);

        $this->externalTask->expects($this->once())->method('getName')->willReturn('foo');

        $result = $this->externalTask->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testRunAndReturnError()
    {
        $files = new FilesCollection([new SplFileInfo('file.php', null, null)]);
        $arguments = m::mock(ArgumentsCollection::class);

        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getActionConfig')->with($this->externalTask)->once()->andReturn([]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn($files);

        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(false);

        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('foo')->once()->andReturn($arguments);
        $this->processBuilder->shouldReceive('buildProcess')->with($arguments, $runner)->once()->andReturn($process);

        $this->processFormatter->shouldReceive('format')->with($process)->once()->andReturn('ERROR');
        $this->externalTask->expects($this->once())->method('getName')->willReturn('foo');

        $result = $this->externalTask->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR', $result->getMessage());
    }
}
