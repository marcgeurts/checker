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
     * @var \ClickNow\Checker\Task\AbstractExternalTask
     */
    protected $task;

    protected function setUp()
    {
        $checker = m::mock(Checker::class);
        $processBuilder = m::mock(ProcessBuilder::class);
        $formatter = m::mock(ProcessFormatterInterface::class);
        $this->task = new FooTask($checker, $processBuilder, $formatter);
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
        $this->assertInstanceOf(OptionsResolver::class, $options);

        $result = $options->getDefinedOptions();
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertContains('foo', $result);
        $this->assertContains('bar', $result);
        $this->assertContains('foobar', $result);
    }

    public function testGetConfigWithMergeDefaultConfig()
    {
        $this->task->mergeDefaultConfig(['foo' => 'bar', 'bar' => 'foo']);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionConfig')->once()->andReturn(['bar' => 'bar', 'foobar' => 'foobar']);

        $result = $this->task->getConfig($command);
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertSame('bar', $result['foo']);
        $this->assertSame('bar', $result['bar']);
        $this->assertSame('foobar', $result['foobar']);
    }

    public function testGetConfigWithoutMergeDefaultConfig()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionConfig')->once()->andReturn(['bar' => 'bar', 'foobar' => 'foobar']);

        $result = $this->task->getConfig($command);
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertNull($result['foo']);
        $this->assertSame('bar', $result['bar']);
        $this->assertSame('foobar', $result['foobar']);
    }

    public function testRun()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionConfig')->once()->andReturn(['bar' => 'bar', 'foobar' => 'foobar']);

        $context = m::mock(ContextInterface::class);
        $result = $this->task->run($command, $context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame($command, $result->getCommand());
        $this->assertSame($context, $result->getContext());
        $this->assertSame($this->task, $result->getAction());
    }
}

class FooTask extends AbstractExternalTask
{
    public function getName()
    {
        return 'foo';
    }

    public function getConfigOptions()
    {
        $options = new OptionsResolver();

        $options->setDefaults([
            'foo'    => null,
            'bar'    => null,
            'foobar' => null,
        ]);

        return $options;
    }

    protected function execute($config, CommandInterface $command, ContextInterface $context)
    {
        return Result::success($command, $context, $this);
    }
}
