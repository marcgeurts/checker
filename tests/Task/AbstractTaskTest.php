<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use Mockery as m;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @group task
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
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionConfig')->with($this->task)->times(5)->andReturn([]);
        $command->shouldReceive('getName')->withNoArgs()->twice()->andReturn('bar');

        $commandContext = m::mock(ContextInterface::class);
        $commandContext->shouldReceive('getName')->withNoArgs()->times(3)->andReturn('foo');

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getCommand')->withNoArgs()->times(3)->andReturn($commandContext);

        $this->assertTrue($this->task->canRunInContext($command, $context));

        $this->task->mergeDefaultConfig(['can_run_in' => false]);
        $this->assertFalse($this->task->canRunInContext($command, $context));

        $this->task->mergeDefaultConfig(['can_run_in' => ['bar']]);
        $this->assertTrue($this->task->canRunInContext($command, $context));

        $this->task->mergeDefaultConfig(['can_run_in' => ['foo']]);
        $this->assertTrue($this->task->canRunInContext($command, $context));

        $this->task->mergeDefaultConfig(['can_run_in' => ['foobar']]);
        $this->assertFalse($this->task->canRunInContext($command, $context));
    }

    public function testRun()
    {
        $files = new FilesCollection([new SplFileInfo('file.php', null, null)]);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionConfig')->with($this->task)->once()->andReturn([]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn($files);

        $this->task
            ->expects($this->once())
            ->method('execute')
            ->willReturn(Result::success($command, $context, $this->task));

        $result = $this->task->run($command, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
    }

    public function testRunWithFilesEmpty()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionConfig')->with($this->task)->once()->andReturn([]);

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $this->task->expects($this->never())->method('execute');

        $result = $this->task->run($command, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSkipped());
    }
}
