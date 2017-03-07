<?php

namespace ClickNow\Checker\Config;

use Mockery as m;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @group  config
 * @covers \ClickNow\Checker\Config\Checker
 */
class CheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|\Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \ClickNow\Checker\Config\Checker
     */
    protected $checker;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->checker = new Checker($this->container);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetBinDir()
    {
        $this->container->shouldReceive('getParameter')->with('bin-dir')->once()->andReturn('./vendor/bin');
        $this->assertSame('./vendor/bin', $this->checker->getBinDir());
    }

    public function testGetGitDir()
    {
        $this->container->shouldReceive('getParameter')->with('git-dir')->once()->andReturn('.');
        $this->assertSame('.', $this->checker->getGitDir());
    }

    public function testGetHooksDir()
    {
        $this->container->shouldReceive('getParameter')->with('hooks-dir')->once()->andReturn('./hooks/');
        $this->assertSame('./hooks/', $this->checker->getHooksDir());
    }

    public function testGetHooksPreset()
    {
        $this->container->shouldReceive('getParameter')->with('hooks-preset')->once()->andReturn('local');
        $this->assertSame('local', $this->checker->getHooksPreset());
    }

    public function testGetProcessTimeout()
    {
        $this->container->shouldReceive('getParameter')->with('process-timeout')->once()->andReturn(60);
        $this->assertSame(60.0, $this->checker->getProcessTimeout());
    }

    public function testGetProcessAsyncWait()
    {
        $this->container->shouldReceive('getParameter')->with('process-async-wait')->once()->andReturn(1000);
        $this->assertSame(1000, $this->checker->getProcessAsyncWait());
    }

    public function testGetProcessAsyncLimit()
    {
        $this->container->shouldReceive('getParameter')->with('process-async-limit')->once()->andReturn(10);
        $this->assertSame(10, $this->checker->getProcessAsyncLimit());
    }

    public function testIsStopOnFailure()
    {
        $this->container->shouldReceive('getParameter')->with('stop-on-failure')->once()->andReturn(true);
        $this->assertTrue($this->checker->isStopOnFailure());
    }

    public function testIsIgnoreUnstagedChanges()
    {
        $this->container->shouldReceive('getParameter')->with('ignore-unstaged-changes')->once()->andReturn(true);
        $this->assertTrue($this->checker->isIgnoreUnstagedChanges());
    }

    public function testIsStrict()
    {
        $this->container->shouldReceive('getParameter')->with('strict')->once()->andReturn(true);
        $this->assertTrue($this->checker->isStrict());
    }

    public function testGetProgress()
    {
        $this->container->shouldReceive('getParameter')->with('progress')->once()->andReturn('list');
        $this->assertSame('list', $this->checker->getProgress());
    }

    public function testIsSkipEmptyOutput()
    {
        $this->container->shouldReceive('getParameter')->with('skip-empty-output')->once()->andReturn(true);
        $this->assertTrue($this->checker->isSkipEmptyOutput());
    }

    public function testIsSkipSuccessOutput()
    {
        $this->container->shouldReceive('getParameter')->with('skip-success-output')->once()->andReturn(true);
        $this->assertTrue($this->checker->isSkipSuccessOutput());
    }

    public function testIsSkipCircumventionOutput()
    {
        $this->container->shouldReceive('getParameter')->with('skip-circumvention-output')->once()->andReturn(true);
        $this->assertTrue($this->checker->isSkipCircumventionOutput());
    }

    public function testGetMessage()
    {
        $this->container->shouldReceive('getParameter')->with('message')->once()->andReturnNull();
        $this->assertNull($this->checker->getMessage('foo'));

        $this->container->shouldReceive('getParameter')->with('message')->once()->andReturn(['foo' => 'bar']);
        $this->assertNull($this->checker->getMessage('bar'));

        $this->container->shouldReceive('getParameter')->with('message')->once()->andReturn(['foo' => 'bar']);
        $this->assertSame('bar', $this->checker->getMessage('foo'));
    }
}
