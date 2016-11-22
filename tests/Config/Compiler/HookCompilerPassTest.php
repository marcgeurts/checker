<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Command\Command;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Exception\TaskNotFoundException;
use ClickNow\Checker\Repository\Git;
use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group config/compiler
 * @covers \ClickNow\Checker\Config\Compiler\HookCompilerPass
 */
class HookCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder|\Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \ClickNow\Checker\Config\Compiler\HookCompilerPass
     */
    protected $hookCompilerPass;

    protected function setUp()
    {
        $this->container = m::mock(ContainerBuilder::class);
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('checker.task')
            ->atMost()
            ->once()
            ->andReturn(['foo' => [['config' => 'foo']]]);

        $this->hookCompilerPass = new HookCompilerPass();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->hookCompilerPass);
        $this->assertInstanceOf(AbstractCompilerPass::class, $this->hookCompilerPass);
    }

    public function testConfigure()
    {
        $times = count(Git::$hooks);

        $definition = m::mock(Definition::class);
        $definition
            ->shouldReceive('addArgument')
            ->with(m::type(Reference::class))
            ->times($times)
            ->andReturnSelf()
            ->ordered('addArgument');

        $definition
            ->shouldReceive('addArgument')
            ->with(m::on(function ($hook) {
                return in_array($hook, Git::$hooks);
            }))
            ->times($times)
            ->andReturnSelf()
            ->ordered('addArgument');

        $definition
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('foo'), []])
            ->once()
            ->andReturnSelf();

        $definition
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('command.bar'), []])
            ->once()
            ->andReturnSelf();

        $definition
            ->shouldReceive('addMethodCall')
            ->with('setConfig', [[]])
            ->times($times)
            ->andReturnSelf();

        $config = ['pre-commit' => ['tasks' => ['foo' => []], 'commands' => ['bar' => []]]];
        $this->container->shouldReceive('getParameter')->with('hooks')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('/^hook./')->times($times)->andReturn(false);
        $this->container->shouldReceive('hasDefinition')->with('command.bar')->once()->andReturn(true);
        $this->container
            ->shouldReceive('register')
            ->with('/^hook./', Command::class)
            ->times($times)
            ->andReturn($definition);

        $this->hookCompilerPass->process($this->container);
    }

    public function testTaskNotFound()
    {
        $this->setExpectedException(TaskNotFoundException::class, 'Task `bar` was not found.');

        $definition = m::mock(Definition::class);
        $definition->shouldReceive('addMethodCall')->with('addAction', [new Reference('bar'), []])->never();
        $definition->shouldReceive('addMethodCall')->with('setConfig', [[]])->andReturnSelf();

        $config = ['pre-commit' => ['tasks' => ['bar' => []]]];
        $this->container->shouldReceive('getParameter')->with('hooks')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('/^hook./')->andReturn(true);
        $this->container->shouldReceive('findDefinition')->with('/^hook./')->andReturn($definition);

        $this->hookCompilerPass->process($this->container);
    }

    public function testCommandNotFound()
    {
        $this->setExpectedException(CommandNotFoundException::class, 'Command `bar` was not found.');

        $definition = m::mock(Definition::class);
        $definition->shouldReceive('addMethodCall')->with('addAction', [new Reference('command.bar'), []])->never();
        $definition->shouldReceive('addMethodCall')->with('setConfig', [[]])->andReturnSelf();

        $config = ['pre-commit' => ['commands' => ['bar' => []]]];
        $this->container->shouldReceive('getParameter')->with('hooks')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('/^hook./')->andReturn(true);
        $this->container->shouldReceive('findDefinition')->with('/^hook./')->andReturn($definition);
        $this->container->shouldReceive('hasDefinition')->with('command.bar')->once()->andReturn(false);

        $this->hookCompilerPass->process($this->container);
    }
}
