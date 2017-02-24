<?php

namespace ClickNow\Checker\Composer;

use Composer\Package\PackageInterface;
use Exception;
use Mockery as m;

/**
 * @group  composer2
 * @covers \ClickNow\Checker\Composer\ComposerUtil
 * @runTestsInSeparateProcesses
 */
class ComposerUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Composer\Package\Loader\JsonLoader|\Mockery\MockInterface
     */
    protected $jsonLoader;

    protected function setUp()
    {
        $this->jsonLoader = m::mock('overload:Composer\Package\Loader\JsonLoader');
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testLoadPackage()
    {
        $this->jsonLoader->shouldReceive('load')->withAnyArgs()->once()->andReturn(m::mock(PackageInterface::class));

        $this->assertInstanceOf(PackageInterface::class, ComposerUtil::loadPackage());
    }

    public function testEnsureProjectBinDirInSystemPath()
    {
        $config = m::mock('Composer\Config');
        $config->shouldReceive('get')->with('bin-dir')->once()->andReturn(__DIR__);

        $factory = m::mock('overload:Composer\Factory');
        $factory->shouldReceive('createConfig')->withNoArgs()->once()->andReturn($config);

        $this->jsonLoader->shouldReceive('load')->withAnyArgs()->once()->andReturn(m::mock(PackageInterface::class));

        $this->assertInstanceOf(PackageInterface::class, ComposerUtil::loadPackage());
    }

    public function testInvalidLoadPackage()
    {
        $this->jsonLoader->shouldReceive('load')->withAnyArgs()->once()->andThrow(Exception::class);

        $this->assertNull(ComposerUtil::loadPackage());
    }
}
