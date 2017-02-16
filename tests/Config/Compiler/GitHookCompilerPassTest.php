<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Exception\TaskNotFoundException;
use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @group  config/compiler
 * @covers \ClickNow\Checker\Config\Compiler\GitHookCompilerPass
 * @covers \ClickNow\Checker\Config\Compiler\AbstractCompilerPass
 */
class GitHookCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Config\Compiler\GitHookCompilerPass
     */
    protected $gitHookCompilerPass;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder|\Mockery\MockInterface
     */
    protected $container;

    protected function setUp()
    {
        $this->gitHookCompilerPass = new GitHookCompilerPass();
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
        $this->assertInstanceOf(CompilerPassInterface::class, $this->gitHookCompilerPass);
        $this->assertInstanceOf(AbstractCompilerPass::class, $this->gitHookCompilerPass);
    }

    public function testConfigure()
    {
        $hook = m::mock(Definition::class);
        $hook
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('foo'), []])
            ->once()
            ->andReturnSelf();

        $hook
            ->shouldReceive('addMethodCall')
            ->with('addAction', [new Reference('runner.command.bar'), []])
            ->once()
            ->andReturnSelf();

        $hook->shouldReceive('addMethodCall')->with('setProcessTimeout', [60])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setProcessAsyncWait', [10])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setProcessAsyncLimit', [1000])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setStopOnFailure', [false])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setIgnoreUnstagedChanges', [false])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setStrict', [false])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setProgress', ['list'])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setSkipSuccessOutput', [false])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setMessage', [['failed' => 'ERROR']])->once()->andReturnSelf();
        $hook->shouldReceive('addMethodCall')->with('setCanRunIn', [true])->once()->andReturnSelf();

        $this->container
            ->shouldReceive('getParameter')
            ->with('git-hooks')
            ->once()
            ->andReturn([
                'pre-commit' => [
                    'tasks'                   => ['foo' => []],
                    'commands'                => ['bar' => []],
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

        $this->container->shouldReceive('findDefinition')->with('runner.git-hook.pre-commit')->once()->andReturn($hook);
        $this->container->shouldReceive('hasDefinition')->with('runner.command.bar')->once()->andReturn(true);

        $this->gitHookCompilerPass->process($this->container);
    }

    public function testTaskNotFound()
    {
        $this->setExpectedException(TaskNotFoundException::class, 'Task `bar` was not found.');

        $config = ['pre-commit' => ['tasks' => ['bar' => []]]];

        $hook = m::mock(Definition::class);
        $hook->shouldReceive('addMethodCall')->with('addAction', [new Reference('bar'), []])->never();

        $this->container->shouldReceive('getParameter')->with('git-hooks')->once()->andReturn($config);
        $this->container->shouldReceive('findDefinition')->with('runner.git-hook.pre-commit')->once()->andReturn($hook);

        $this->gitHookCompilerPass->process($this->container);
    }

    public function testCommandNotFound()
    {
        $this->setExpectedException(CommandNotFoundException::class, 'Command `bar` was not found.');

        $config = ['pre-commit' => ['commands' => ['bar' => []]]];

        $hook = m::mock(Definition::class);
        $hook->shouldReceive('addMethodCall')->with('addAction', [new Reference('runner.command.bar'), []])->never();

        $this->container->shouldReceive('getParameter')->with('git-hooks')->once()->andReturn($config);
        $this->container->shouldReceive('findDefinition')->with('runner.git-hook.pre-commit')->once()->andReturn($hook);
        $this->container->shouldReceive('hasDefinition')->with('runner.command.bar')->once()->andReturn(false);

        $this->gitHookCompilerPass->process($this->container);
    }
}
