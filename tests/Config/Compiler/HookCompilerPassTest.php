<?php

namespace ClickNow\Checker\Config\Compiler;

use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        /*$definition = m::mock(Definition::class);
        //$definition->shouldReceive('addMethodCall')->with('mergeDefaultConfig', [[]])->once()->andReturnNull();
        //$definition->shouldReceive('addMethodCall')->with('mergeDefaultConfig', [[]])->once()->andReturnNull();

        $config = ['pre-commit' => ['tasks' => ['foo' => []], 'commands' => ['bar' => []]]];
        $this->container->shouldReceive('getParameter')->with('hooks')->once()->andReturn($config);
        $this->container->shouldReceive('hasDefinition')->once()->andReturn(false);
        $this->container->shouldReceive('register')->once()->andReturn($definition);
        $this->hookCompilerPass->process($this->container);*/
    }
}
