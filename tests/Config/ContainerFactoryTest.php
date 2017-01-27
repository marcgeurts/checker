<?php

namespace ClickNow\Checker\Config;

use ClickNow\Checker\Config\Compiler\CommandCompilerPass;
use ClickNow\Checker\Config\Compiler\ExtensionCompilerPass;
use ClickNow\Checker\Config\Compiler\GitHookCompilerPass;
use ClickNow\Checker\Config\Compiler\TaskCompilerPass;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * @group  config
 * @covers \ClickNow\Checker\Config\ContainerFactory
 */
class ContainerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $tempFile;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;

    protected function setUp()
    {
        $this->tempFile = __DIR__.'/config.yml';
        file_put_contents($this->tempFile, "parameters:\n  foo: bar");
        $this->container = ContainerFactory::create($this->tempFile);
    }

    protected function tearDown()
    {
        unlink($this->tempFile);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    public function testLoadServices()
    {
        $this->assertContains(new FileResource($this->tempFile), $this->container->getResources(), '', false, false);
    }

    public function testRegisterCompilerPass()
    {
        $result = $this->container->getCompilerPassConfig()->getBeforeOptimizationPasses();

        $this->assertInternalType('array', $result);
        $this->assertCount(5, $result);
        $this->assertInstanceOf(ExtensionCompilerPass::class, $result[0]);
        $this->assertInstanceOf(TaskCompilerPass::class, $result[1]);
        $this->assertInstanceOf(CommandCompilerPass::class, $result[2]);
        $this->assertInstanceOf(GitHookCompilerPass::class, $result[3]);
        $this->assertInstanceOf(RegisterListenersPass::class, $result[4]);
    }

    public function testGetParameterOnLoadedTempFile()
    {
        $this->assertSame('bar', $this->container->getParameter('foo'));
    }
}
