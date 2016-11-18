<?php

namespace ClickNow\Checker\Config\Compiler;

use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @group config/compiler
 * @covers \ClickNow\Checker\Config\Compiler\TaskCompilerPass
 */
class TaskCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder|\Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \ClickNow\Checker\Config\Compiler\TaskCompilerPass
     */
    protected $taskCompilerPass;

    protected function setUp()
    {
        $this->container = m::mock(ContainerBuilder::class);
        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with(TaskCompilerPass::TAG_TASK)
            ->atMost()
            ->once()
            ->andReturn(['foo' => [['config' => 'foo']]]);

        $this->taskCompilerPass = new TaskCompilerPass();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->taskCompilerPass);
        $this->assertInstanceOf(AbstractCompilerPass::class, $this->taskCompilerPass);
    }

    public function testA()
    {
        $this->container->shouldReceive('getParameter')->with('tasks')->once()->andReturn(['foo' => []]);

        $definition = m::mock(Definition::class);
        $definition->shouldReceive('addMethodCall')->with('mergeDefaultConfig', [[]])->once()->andReturn();

        $this->container->shouldReceive('findDefinition')->with('foo')->once()->andReturn($definition);
        $this->taskCompilerPass->process($this->container);
    }

    public function testAA()
    {
        $this->container->shouldReceive('getParameter')->with('tasks')->once()->andReturn(['foo' => []]);

        $definition = m::mock(Definition::class);
        $definition->shouldReceive('addMethodCall')->with('mergeDefaultConfig', [[]])->once()->andReturn();

        $this->container->shouldReceive('findDefinition')->with('foo')->once()->andReturn($definition);
        $this->taskCompilerPass->process($this->container);
    }
}
