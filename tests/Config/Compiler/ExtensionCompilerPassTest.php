<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\ExtensionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ExtensionInvalidException;
use ClickNow\Checker\Exception\ExtensionNotFoundException;
use ClickNow\Checker\Extension\ExtensionInterface;
use Mockery as m;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @group config/compiler
 * @covers \ClickNow\Checker\Config\Compiler\ExtensionCompilerPass
 */
class ExtensionCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder|\Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \ClickNow\Checker\Config\Compiler\ExtensionCompilerPass
     */
    protected $extensionCompilerPass;

    protected function setUp()
    {
        $this->container = m::mock(ContainerBuilder::class);
        $this->extensionCompilerPass = new ExtensionCompilerPass();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->extensionCompilerPass);
        $this->assertInstanceOf(AbstractCompilerPass::class, $this->extensionCompilerPass);
    }

    public function testConfigure()
    {
        $extension = $this->getMock(ExtensionInterface::class);
        $this->container->shouldReceive('getParameter')->with('extensions')->once()->andReturn([get_class($extension)]);
        $this->extensionCompilerPass->process($this->container);
    }

    public function testExtensionNotFound()
    {
        $this->setExpectedException(ExtensionNotFoundException::class, 'Extension `bar` was not found.');

        $this->container->shouldReceive('getParameter')->with('extensions')->once()->andReturn(['bar']);
        $this->extensionCompilerPass->process($this->container);
    }

    public function testExtensionAlreadyRegistered()
    {
        $extension = $this->getMock(ExtensionInterface::class);
        $class = get_class($extension);

        $this->setExpectedException(
            ExtensionAlreadyRegisteredException::class,
            'Extension `'.$class.'` already registered.'
        );

        $this->container->shouldReceive('getParameter')->with('extensions')->once()->andReturn([$class, $class]);
        $this->extensionCompilerPass->process($this->container);
    }

    public function testExtensionInvalid()
    {
        $this->setExpectedException(
            ExtensionInvalidException::class,
            'Extension `stdClass` must implement ExtensionInterface.'
        );

        $this->container->shouldReceive('getParameter')->with('extensions')->once()->andReturn([\stdClass::class]);
        $this->extensionCompilerPass->process($this->container);
    }
}
