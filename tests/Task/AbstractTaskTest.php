<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\AbstractTask
 */
class AbstractTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Task\AbstractTask|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $task;

    protected function setUp()
    {
        $this->task = $this->getMockForAbstractClass(AbstractTask::class);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(TaskInterface::class, $this->task);
    }

    public function testGetName()
    {
        $this->task->expects($this->once())->method('getName')->willReturn('foo');

        $this->assertSame('foo', $this->task->getName());
    }

    public function testCanRunInContext()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getActionConfig')->with($this->task)->times(5)->andReturn([]);
        $runner->shouldReceive('getName')->withNoArgs()->twice()->andReturn('bar');

        $runnerContext = m::mock(ContextInterface::class);
        $runnerContext->shouldReceive('getName')->withNoArgs()->times(3)->andReturn('foo');

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getRunner')->withNoArgs()->times(3)->andReturn($runnerContext);

        $this->assertTrue($this->task->canRunInContext($runner, $context));

        $this->task->mergeDefaultConfig(['can-run-in' => false]);
        $this->assertFalse($this->task->canRunInContext($runner, $context));

        $this->task->mergeDefaultConfig(['can-run-in' => ['bar']]);
        $this->assertTrue($this->task->canRunInContext($runner, $context));

        $this->task->mergeDefaultConfig(['can-run-in' => ['foo']]);
        $this->assertTrue($this->task->canRunInContext($runner, $context));

        $this->task->mergeDefaultConfig(['can-run-in' => ['foobar']]);
        $this->assertFalse($this->task->canRunInContext($runner, $context));
    }

    public function testRun()
    {
        $files = new FilesCollection([new SplFileInfo('file.php', null, null)]);

        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getActionConfig')->with($this->task)->once()->andReturn([]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn($files);

        $this->task
            ->expects($this->once())
            ->method('execute')
            ->willReturn(Result::success($runner, $context, $this->task));

        $result = $this->task->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
    }

    public function testRunWithFilesEmpty()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getActionConfig')->with($this->task)->once()->andReturn([]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $this->task->expects($this->never())->method('execute');

        $result = $this->task->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSkipped());
    }

    public function testRunWithAlwaysExecute()
    {
        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getActionConfig')->with($this->task)->once()->andReturn(['always-execute' => true]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $this->task
            ->expects($this->once())
            ->method('execute')
            ->willReturn(Result::success($runner, $context, $this->task));

        $result = $this->task->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
    }

    public function testRunWithFinderFiles()
    {
        $finder = [
            'name'       => ['file1.*'],
            'not-name'   => ['file2.*'],
            'path'       => ['path1'],
            'not-path'   => ['path2'],
            'extensions' => ['php'],
        ];

        $runner = m::mock(RunnerInterface::class);
        $runner->shouldReceive('getActionConfig')->with($this->task)->once()->andReturn(['finder' => $finder]);

        $files = new FilesCollection([
            new SplFileInfo('path1/file1.php', 'path1', 'path1/file1.php'),
            new SplFileInfo('path1/file2.php', 'path1', 'path1/file2.php'),
            new SplFileInfo('path2/file1.php', 'path2', 'path2/file1.php'),
            new SplFileInfo('path2/file2.php', 'path2', 'path2/file2.php'),
            new SplFileInfo('path1/file1.txt', 'path1', 'path1/file1.txt'),
        ]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn($files);

        $this->task
            ->expects($this->once())
            ->method('execute')
            ->willReturn(Result::success($runner, $context, $this->task));

        $result = $this->task->run($runner, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
    }
}
