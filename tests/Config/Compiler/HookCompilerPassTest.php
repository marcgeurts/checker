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
 * @covers \ClickNow\Checker\Config\Compiler\AbstractCompilerPass
 */
class HookCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Config\Compiler\HookCompilerPass
     */
    protected $hookCompilerPass;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder|\Mockery\MockInterface
     */
    protected $container;

    protected function setUp()
    {
        $this->hookCompilerPass = new HookCompilerPass();
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
        $this->assertInstanceOf(CompilerPassInterface::class, $this->hookCompilerPass);
        $this->assertInstanceOf(AbstractCompilerPass::class, $this->hookCompilerPass);
    }

    public function testConfigure()
    {
        $times = count(Git::$hooks);
        $config = ['pre-commit' => ['tasks' => ['foo' => []], 'commands' => ['bar' => []]]];

        $hook = m::mock(Definition::class);
        $hook
            ->shouldReceive('addArgument')
            ->with(m::type(Reference::class))
            ->times($times)
            ->andReturnSelf()
            ->ordered('addArgument');

        $hook
            ->shouldReceive('addArgument')
            ->with(m::on(function ($hook) {
                return in_array($hook, Git::$hooks);
            }))
            ->times($times)
            ->andReturnSelf()
            ->ordered('addArgument');

        $hook
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('foo'), []])
            ->once()
            ->andReturnSelf();

        $hook
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('command.bar'), []])
            ->once()
            ->andReturnSelf();

        $hook
            ->shouldReceive('addMethodCall')
            ->with('setConfig', [[]])
            ->times($times)
            ->andReturnSelf();

        $this->container->shouldReceive('getParameter')->with('hooks')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('/^hook./')->times($times)->andReturn(false);
        $this->container->shouldReceive('hasDefinition')->with('command.bar')->once()->andReturn(true);
        $this->container->shouldReceive('register')->with('/^hook./', Command::class)->times($times)->andReturn($hook);

        $this->hookCompilerPass->process($this->container);
    }

    public function testTaskNotFound()
    {
        $this->setExpectedException(TaskNotFoundException::class, 'Task `bar` was not found.');

        $config = ['pre-commit' => ['tasks' => ['bar' => []]]];

        $hook = m::mock(Definition::class);
        $hook->shouldReceive('addMethodCall')->with('addAction', [new Reference('bar'), []])->never();
        $hook->shouldReceive('addMethodCall')->with('setConfig', [[]])->andReturnSelf();

        $this->container->shouldReceive('getParameter')->with('hooks')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('/^hook./')->andReturn(true);
        $this->container->shouldReceive('findDefinition')->with('/^hook./')->andReturn($hook);

        $this->hookCompilerPass->process($this->container);
    }

    public function testCommandNotFound()
    {
        $this->setExpectedException(CommandNotFoundException::class, 'Command `bar` was not found.');

        $config = ['pre-commit' => ['commands' => ['bar' => []]]];

        $hook = m::mock(Definition::class);
        $hook->shouldReceive('addMethodCall')->with('addAction', [new Reference('command.bar'), []])->never();
        $hook->shouldReceive('addMethodCall')->with('setConfig', [[]])->andReturnSelf();

        $this->container->shouldReceive('getParameter')->with('hooks')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->with('/^hook./')->andReturn(true);
        $this->container->shouldReceive('findDefinition')->with('/^hook./')->andReturn($hook);
        $this->container->shouldReceive('hasDefinition')->with('command.bar')->once()->andReturn(false);

        $this->hookCompilerPass->process($this->container);
    }
}
