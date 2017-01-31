<?php

namespace ClickNow\Checker\Console;

use ClickNow\Checker\Repository\Filesystem;
use Composer\Package\PackageInterface;
use Mockery as m;

/**
 * @group console
 * @covers \ClickNow\Checker\Console\ConfigFile
 */
class ConfigFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Repository\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var \Composer\Package\PackageInterface|\Mockery\MockInterface
     */
    protected $package;

    /**
     * @var \ClickNow\Checker\Console\ConfigFile
     */
    protected $configFile;

    protected function setUp()
    {
        $this->filesystem = m::mock(Filesystem::class);
        $this->package = m::mock(PackageInterface::class);
        $this->configFile = new ConfigFile($this->filesystem, $this->package);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testGetDefaultPath()
    {
        $this->filesystem->shouldReceive('exists')->with('/checker.yml.dist$/')->once()->andReturn(false);
        $this->filesystem->shouldReceive('isAbsolutePath')->with('/checker.yml$/')->once()->andReturn(true);

        $this->package->shouldReceive('getExtra')->withNoArgs()->once()->andReturnNull();

        $this->assertSame(getcwd().DIRECTORY_SEPARATOR.'checker.yml', $this->configFile->getDefaultPath());
    }

    public function testConfigPathFromComposer()
    {
        $this->filesystem->shouldReceive('exists')->with('foo.dist')->once()->andReturn(false);
        $this->filesystem->shouldReceive('isAbsolutePath')->with('foo')->once()->andReturn(true);

        $this->package->shouldReceive('getExtra')->withNoArgs()->once()->andReturn(['checker' => ['config' => 'foo']]);

        $this->assertSame('foo', $this->configFile->getDefaultPath());
    }

    public function testConfigPathWithoutAbsolutePath()
    {
        $this->filesystem->shouldReceive('exists')->with('foo.dist')->once()->andReturn(false);
        $this->filesystem->shouldReceive('isAbsolutePath')->with('foo')->once()->andReturn(false);

        $this->package->shouldReceive('getExtra')->withNoArgs()->once()->andReturn(['checker' => ['config' => 'foo']]);

        $this->assertSame(getcwd().DIRECTORY_SEPARATOR.'foo', $this->configFile->getDefaultPath());
    }

    public function testConfigPathWithDistSupport()
    {
        $this->filesystem->shouldReceive('exists')->with('/checker.yml.dist$/')->once()->andReturn(true);
        $this->filesystem->shouldReceive('isAbsolutePath')->with('/checker.yml.dist$/')->once()->andReturn(true);

        $this->package->shouldReceive('getExtra')->withNoArgs()->once()->andReturnNull();

        $this->assertSame(getcwd().DIRECTORY_SEPARATOR.'checker.yml.dist', $this->configFile->getDefaultPath());
    }
}
