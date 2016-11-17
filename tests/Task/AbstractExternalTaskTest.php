<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Formatter\ProcessFormatterInterface;
use ClickNow\Checker\Process\ProcessBuilder;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use Mockery as m;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @group task
 * @covers \ClickNow\Checker\Task\AbstractExternalTask
 */
class AbstractExternalTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Task\AbstractExternalTask|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $task;

    protected function setUp()
    {
        $this->task = $this->getMockForAbstractClass(AbstractExternalTask::class, [
            m::mock(Checker::class),
            m::mock(ProcessBuilder::class),
            m::mock(ProcessFormatterInterface::class),
        ]);

        $resolver = new OptionsResolver();
        $resolver->setDefaults(['foo' => null, 'bar' => null, 'foobar' => null]);
        $this->task->expects($this->atMost(1))->method('getConfigOptions')->willReturn($resolver);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(TaskInterface::class, $this->task);
        $this->assertInstanceOf(AbstractExternalTask::class, $this->task);
    }

    public function testGetName()
    {
        $this->task->expects($this->once())->method('getName')->willReturn('foo');

        $this->assertSame('foo', $this->task->getName());
    }

    public function testCanRunInContext()
    {
        $command = m::mock(CommandInterface::class);
        $context = m::mock(ContextInterface::class);

        $this->assertTrue($this->task->canRunInContext($command, $context));
    }

    public function testGetConfigOption()
    {
        $options = $this->task->getConfigOptions();
        $result = $options->getDefinedOptions();

        $this->assertInstanceOf(OptionsResolver::class, $options);
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertContains('foo', $result);
        $this->assertContains('bar', $result);
        $this->assertContains('foobar', $result);
    }

    public function testGetConfigWithMergeDefaultConfig()
    {
        $this->task->mergeDefaultConfig(['foo' => 'bar', 'bar' => 'foo']);
        $result = $this->task->getConfig($this->mockCommand());

        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertSame('bar', $result['foo']);
        $this->assertSame('bar', $result['bar']);
        $this->assertSame('foobar', $result['foobar']);
    }

    public function testGetConfigWithoutMergeDefaultConfig()
    {
        $result = $this->task->getConfig($this->mockCommand());

        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertNull($result['foo']);
        $this->assertSame('bar', $result['bar']);
        $this->assertSame('foobar', $result['foobar']);
    }

    public function testRun()
    {
        $command = $this->mockCommand();
        $context = m::mock(ContextInterface::class);

        $this->task
            ->expects($this->once())
            ->method('execute')
            ->willReturn(Result::success($command, $context, $this->task));

        $result = $this->task->run($command, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame($command, $result->getCommand());
        $this->assertSame($context, $result->getContext());
        $this->assertSame($this->task, $result->getAction());
    }

    /**
     * Mock command.
     *
     * @return \ClickNow\Checker\Command\CommandInterface|\Mockery\MockInterface
     */
    protected function mockCommand()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionConfig')->once()->andReturn(['bar' => 'bar', 'foobar' => 'foobar']);

        return $command;
    }
}
