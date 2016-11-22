<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Command\Command;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\TaskNotFoundException;
use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group config/compiler
 * @covers \ClickNow\Checker\Config\Compiler\CommandCompilerPass
 */
class CommandCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder|\Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \ClickNow\Checker\Config\Compiler\CommandCompilerPass
     */
    protected $commandCompilerPass;

    protected function setUp()
    {
        $this->container = m::mock(ContainerBuilder::class);
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('checker.task')
            ->atMost()
            ->once()
            ->andReturn(['foo' => [['config' => 'foo']]]);

        $this->commandCompilerPass = new CommandCompilerPass();
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
        $config = ['bar' => ['tasks' => ['foo' => []], 'commands' => ['foobar' => []]]];

        $collection = m::mock(Definition::class);
        $command = m::mock(Definition::class);
        $taskReference = new Reference('foo');
        $commandReference = new Reference('command.foobar');

        $this->container->shouldReceive('findDefinition')->with('commands_collection')->once()->andReturn($collection);
        $this->container->shouldReceive('getParameter')->with('commands')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('command.bar')->once()->andReturn(false);
        $this->container->shouldReceive('register')->with('command.bar', Command::class)->once()->andReturn($command);
        $this->container->shouldReceive('findDefinition')->with('command.bar')->once()->andReturn($command);
        $this->container->shouldReceive('hasDefinition')->with('command.foobar')->once()->andReturn(true);

        $collection->shouldReceive('addMethodCall')->with('set', ['bar', $command])->once()->andReturnSelf();

        $command->shouldReceive('addArgument')->with(m::type(Reference::class))->once()->andReturnSelf()->ordered();
        $command->shouldReceive('addArgument')->with('bar')->once()->andReturnSelf()->ordered();
        $command->shouldReceive('addMethodCall')->with('addAction', [$taskReference, []])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('setConfig', [[]])->once()->andReturnSelf();
        $command->shouldReceive('addMethodCall')->with('addAction', [$commandReference, []])->once()->andReturnSelf();

        $this->commandCompilerPass->process($this->container);
    }

    public function testCommandInvalid()
    {
        $this->setExpectedException(
            CommandInvalidException::class,
            'The name of a command `foo` can not be the same as the name of a task.'
        );

        $config = ['foo' => []];

        $collection = m::mock(Definition::class);
        $collection->shouldReceive('addMethodCall')->with('set', m::any())->never();

        $this->container->shouldReceive('findDefinition')->with('commands_collection')->once()->andReturn($collection);
        $this->container->shouldReceive('getParameter')->with('commands')->once()->andReturn($config);

        $this->commandCompilerPass->process($this->container);
    }

    public function testTaskNotFound()
    {
        $this->setExpectedException(TaskNotFoundException::class, 'Task `bar` was not found.');

        $config = ['foobar' => ['tasks' => ['bar' => []]]];

        $collection = m::mock(Definition::class);
        $command = m::mock(Definition::class);

        $this->container->shouldReceive('findDefinition')->with('commands_collection')->once()->andReturn($collection);
        $this->container->shouldReceive('getParameter')->with('commands')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('command.foobar')->once()->andReturn(true);
        $this->container->shouldReceive('findDefinition')->with('command.foobar')->once()->andReturn($command);

        $collection->shouldReceive('addMethodCall')->with('set', ['foobar', $command])->never();

        $command->shouldReceive('addMethodCall')->with('addAction', [new Reference('bar'), []])->never();

        $this->commandCompilerPass->process($this->container);
    }

    public function testCommandNotFound()
    {
        
    }
}
