<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Formatter\ProcessFormatterInterface;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Process\ProcessBuilder;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Filesystem;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Process\Process;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\AbstractExternalTask
 */
class AbstractExternalTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \ClickNow\Checker\Repository\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

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

    /**
     * @var string
     */
    protected $class = AbstractExternalTask::class;

    protected function setUp()
    {
        $this->io = m::mock(IOInterface::class);
        $this->io->shouldReceive('isDebug')->withNoArgs()->andReturn(false);
        $this->io->shouldReceive('isQuiet')->withNoArgs()->andReturn(false);
        $this->io->shouldReceive('isVerbose')->withNoArgs()->andReturn(false);
        $this->io->shouldReceive('isDecorated')->withNoArgs()->andReturn(true);

        $this->filesystem = m::mock(Filesystem::class);
        $this->processBuilder = m::mock(ProcessBuilder::class);
        $this->processFormatter = m::mock(ProcessFormatterInterface::class);
        $this->externalTask = $this->mockExternalTask();
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
        $config = $this->getActionConfig();
        $args = new ArgumentsCollection();

        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isStopOnFailure')->withNoArgs()->andReturn(false);
        $runner->shouldReceive('getActionConfig')->with($this->externalTask)->once()->andReturn($config);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $process = m::spy(Process::class);
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(true);

        $this->mockArguments($args);
        $this->processBuilder->shouldReceive('buildProcess')->with($args, $runner)->once()->andReturn($process);
        $this->processFormatter->shouldReceive('format')->with($process)->never();

        $result = $this->externalTask->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testRunAndReturnError()
    {
        $config = $this->getActionConfig();
        $args = new ArgumentsCollection();

        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('isStopOnFailure')->withNoArgs()->andReturn(false);
        $runner->shouldReceive('getActionConfig')->with($this->externalTask)->once()->andReturn($config);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $process = m::spy(Process::class);
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(false);

        $this->mockArguments($args);
        $this->processBuilder->shouldReceive('buildProcess')->with($args, $runner)->once()->andReturn($process);
        $this->processFormatter->shouldReceive('format')->with($process)->once()->andReturn('ERROR');

        $result = $this->externalTask->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR', $result->getMessage());
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\AbstractExternalTask|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        if ($this->class === AbstractExternalTask::class) {
            return $this->getMockForAbstractClass($this->class, [
                $this->io,
                $this->filesystem,
                $this->processBuilder,
                $this->processFormatter,
            ]);
        }

        return $this->getMock($this->class, null, [
            $this->io,
            $this->filesystem,
            $this->processBuilder,
            $this->processFormatter,
        ]);
    }

    /**
     * Mock arguments
     *
     * @param \ClickNow\Checker\Process\ArgumentsCollection $args
     *
     * @return void
     */
    protected function mockArguments(ArgumentsCollection $args)
    {
        $this->externalTask->expects($this->once())->method('createArguments')->willReturn($args);
    }

    /**
     * Get action config.
     *
     * @return array
     */
    protected function getActionConfig()
    {
        return ['always-execute' => true];
    }
}
