<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Exception\TaskNotFoundException;
use ClickNow\Checker\Runner\Runner;
use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group  config/compiler
 * @covers \ClickNow\Checker\Config\Compiler\CommandCompilerPass
 * @covers \ClickNow\Checker\Config\Compiler\AbstractCompilerPass
 */
class CommandCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Config\Compiler\CommandCompilerPass
     */
    protected $commandCompilerPass;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder|\Mockery\MockInterface
     */
    protected $container;

    protected function setUp()
    {
        $this->commandCompilerPass = new CommandCompilerPass();
        $this->container = m::mock(ContainerBuilder::class);
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('checker.task')
            ->atMost()
            ->once()
            ->andReturn(['foo' => [['config' => 'foo']]]);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->commandCompilerPass);
        $this->assertInstanceOf(AbstractCompilerPass::class, $this->commandCompilerPass);
    }

    public function testConfigure()
    {
        $command = m::mock(Definition::class);
        $command->shouldReceive('addArgument')->with(m::type(Reference::class))->once()->andReturnSelf()->ordered();
        $command->shouldReceive('addArgument')->with('bar')->once()->andReturnSelf()->ordered();

        $command
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('foo'), []])
            ->once()
            ->andReturnSelf();

        $command
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('runner.command.foobar'), []])
            ->once()
            ->andReturnSelf();

        $command->shouldReceive('addMethodCall')->with('setProcessTimeout', [60])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setProcessAsyncWait', [10])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setProcessAsyncLimit', [1000])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setStopOnFailure', [false])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setIgnoreUnstagedChanges', [false])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setStrict', [false])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setProgress', ['list'])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setSkipSuccessOutput', [false])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setMessage', [['failed' => 'ERROR']])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setCanRunIn', [true])->once()->andReturnSelf();

        $collection = m::mock(Definition::class);
        $collection->shouldReceive('addMethodCall')->with('set', ['bar', $command])->once()->andReturnSelf();

        $this->container
            ->shouldReceive('findDefinition')
            ->with('runner.commands-collection')
            ->once()
            ->andReturn($collection);

        $this->container
            ->shouldReceive('getParameter')
            ->with('commands')
            ->once()
            ->andReturn([
                'bar' => [
                    'tasks'                   => ['foo' => []],
                    'commands'                => ['foobar' => []],
                    'process-timeout'         => 60,
                    'process-async-wait'      => 10,
                    'process-async-limit'     => 1000,
                    'stop-on-failure'         => false,
                    'ignore-unstaged-changes' => false,
                    'strict'                  => false,
                    'progress'                => 'list',
                    'skip-success-output'     => false,
                    'message'                 => ['failed' => 'ERROR'],
                    'can-run-in'              => true,
                ],
            ]);

        $this->container
            ->shouldReceive('register')
            ->with('runner.command.bar', Runner::class)
            ->once()
            ->andReturn($command);

        $this->container->shouldReceive('findDefinition')->with('runner.command.bar')->once()->andReturn($command);
        $this->container->shouldReceive('hasDefinition')->with('runner.command.foobar')->once()->andReturn(true);

        $this->commandCompilerPass->process($this->container);
    }

    public function testCommandInvalid()
    {
        $this->setExpectedException(
            CommandInvalidException::class,
            'The name of a command `foo` can not be the same as the name of a task.'
        );

        $collection = m::mock(Definition::class);
        $collection->shouldReceive('addMethodCall')->with('set', ['foo', m::type(Runner::class)])->never();

        $this->container
            ->shouldReceive('findDefinition')
            ->with('runner.commands-collection')
            ->once()
            ->andReturn($collection);

        $this->container->shouldReceive('getParameter')->with('commands')->once()->andReturn(['foo' => []]);
        $this->container->shouldReceive('register')->with('runner.command.foo', Runner::class)->never();
        $this->container->shouldReceive('findDefinition')->with('runner.command.foo')->never();

        $this->commandCompilerPass->process($this->container);
    }

    public function testTaskNotFound()
    {
        $this->setExpectedException(TaskNotFoundException::class, 'Task `bar` was not found.');

        $command = m::mock(Definition::class);
        $command->shouldReceive('addArgument')->with(m::type(Reference::class))->once()->andReturnSelf()->ordered();
        $command->shouldReceive('addArgument')->with('foobar')->once()->andReturnSelf()->ordered();
        $command->shouldReceive('addMethodCall')->with('addAction', [new Reference('bar'), []])->never();

        $collection = m::mock(Definition::class);
        $collection->shouldReceive('addMethodCall')->with('set', ['foobar', $command])->never();

        $this->container
            ->shouldReceive('findDefinition')
            ->with('runner.commands-collection')
            ->once()
            ->andReturn($collection);

        $this->container
            ->shouldReceive('getParameter')
            ->with('commands')
            ->once()
            ->andReturn(['foobar' => ['tasks' => ['bar' => []]]]);

        $this->container
            ->shouldReceive('register')
            ->with('runner.command.foobar', Runner::class)
            ->once()
            ->andReturn($command);

        $this->container
            ->shouldReceive('findDefinition')
            ->with('runner.command.foobar')
            ->never();

        $this->commandCompilerPass->process($this->container);
    }

    public function testCommandNotFound()
    {
        $this->setExpectedException(CommandNotFoundException::class, 'Command `bar` was not found.');

        $command = m::mock(Definition::class);
        $command->shouldReceive('addArgument')->with(m::type(Reference::class))->once()->andReturnSelf()->ordered();
        $command->shouldReceive('addArgument')->with('foobar')->once()->andReturnSelf()->ordered();
        $command->shouldReceive('addMethodCall')->with('addAction', [new Reference('runner.command.bar'), []])->never();

        $collection = m::mock(Definition::class);
        $collection->shouldReceive('addMethodCall')->with('set', ['foobar', $command])->once()->andReturnSelf();

        $this->container
            ->shouldReceive('findDefinition')
            ->with('runner.commands-collection')
            ->once()
            ->andReturn($collection);

        $this->container
            ->shouldReceive('getParameter')
            ->with('commands')
            ->once()
            ->andReturn(['foobar' => ['commands' => ['bar' => []]]]);

        $this->container
            ->shouldReceive('register')
            ->with('runner.command.foobar', Runner::class)
            ->once()
            ->andReturn($command);

        $this->container->shouldReceive('findDefinition')->with('runner.command.foobar')->once()->andReturn($command);
        $this->container->shouldReceive('hasDefinition')->with('runner.command.bar')->once()->andReturn(false);

        $this->commandCompilerPass->process($this->container);
    }
}
