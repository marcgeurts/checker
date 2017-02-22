<?php

namespace ClickNow\Checker\Helper;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Console\ConfigFile;
use ClickNow\Checker\Exception\DirectoryNotFoundException;
use ClickNow\Checker\Exception\FileNotFoundException;
use ClickNow\Checker\Process\ExecutableFinder;
use ClickNow\Checker\Repository\Filesystem;
use Mockery as m;
use SplFileInfo;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperInterface;

/**
 * @group  helper
 * @covers \ClickNow\Checker\Helper\PathsHelper
 */
class PathsHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Config\Checker|\Mockery\MockInterface
     */
    protected $checker;

    /**
     * @var \ClickNow\Checker\Repository\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var \ClickNow\Checker\Process\ExecutableFinder|\Mockery\MockInterface
     */
    protected $executableFinder;

    /**
     * @var \ClickNow\Checker\Console\ConfigFile|\Mockery\MockInterface
     */
    protected $configFile;

    /**
     * @var \ClickNow\Checker\Helper\PathsHelper
     */
    protected $pathsHelper;

    protected function setUp()
    {
        if (!defined('CHECKER_PATH')) {
            define('CHECKER_PATH', './');
        }

        $this->checker = m::mock(Checker::class);
        $this->filesystem = m::mock(Filesystem::class);
        $this->executableFinder = m::mock(ExecutableFinder::class);
        $this->configFile = m::mock(ConfigFile::class);
        $this->pathsHelper = new PathsHelper(
            $this->checker,
            $this->filesystem,
            $this->executableFinder,
            $this->configFile
        );
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(HelperInterface::class, $this->pathsHelper);
        $this->assertInstanceOf(Helper::class, $this->pathsHelper);
    }

    public function testGetName()
    {
        $this->assertSame('paths', $this->pathsHelper->getName());
    }

    public function testGetProjectPath()
    {
        $this->assertSame('./', $this->pathsHelper->getProjectPath());
    }

    public function testGetResourcesPath()
    {
        $this->assertSame('./resources/', $this->pathsHelper->getResourcesPath());
    }

    public function testGetAsciiPath()
    {
        $this->assertSame('./resources/ascii/', $this->pathsHelper->getAsciiPath());
    }

    public function testGetMessage()
    {
        $type = m::type(SplFileInfo::class);

        $this->filesystem->shouldReceive('exists')->with('user')->once()->andReturn(true);
        $this->filesystem->shouldReceive('readFromFileInfo')->with($type)->once()->andReturn('foo');

        $this->filesystem->shouldReceive('exists')->with('file')->once()->andReturn(false);
        $this->filesystem->shouldReceive('exists')->with('./resources/ascii/file')->once()->andReturn(true);
        $this->filesystem->shouldReceive('readFromFileInfo')->with($type)->once()->andReturn('bar');

        $this->filesystem->shouldReceive('exists')->with('foobar')->once()->andReturn(false);
        $this->filesystem->shouldReceive('exists')->with('./resources/ascii/foobar')->once()->andReturn(false);

        $this->assertNull($this->pathsHelper->getMessage(null));
        $this->assertSame('foo', $this->pathsHelper->getMessage('user'));
        $this->assertSame('bar', $this->pathsHelper->getMessage('file'));
        $this->assertSame('foobar', $this->pathsHelper->getMessage('foobar'));
    }

    public function testGetWorkingDir()
    {
        $this->assertSame(getcwd(), $this->pathsHelper->getWorkingDir());
    }

    public function testGetGitDir()
    {
        $this->checker->shouldReceive('getGitDir')->withNoArgs()->once()->andReturn('.');

        $this->filesystem->shouldReceive('exists')->with('.')->once()->andReturn(true);
        $this->filesystem->shouldReceive('makePathRelative')->with(realpath('.'), getcwd())->once()->andReturn('./');

        $this->assertSame('./', $this->pathsHelper->getGitDir());
    }

    public function testGetGitDirNotFound()
    {
        $this->setExpectedException(
            DirectoryNotFoundException::class,
            'The configured GIT directory `.` could not be found.'
        );

        $this->checker->shouldReceive('getGitDir')->withNoArgs()->once()->andReturn('.');

        $this->filesystem->shouldReceive('exists')->with('.')->once()->andReturn(false);
        $this->filesystem->shouldReceive('makePathRelative')->with(realpath('.'), getcwd())->never();

        $this->pathsHelper->getGitDir();
    }

    public function testGetGitHookExecutionPath()
    {
        $this->checker->shouldReceive('getGitDir')->withNoArgs()->once()->andReturn('.');

        $this->filesystem->shouldReceive('exists')->with('.')->once()->andReturn(true);
        $this->filesystem->shouldReceive('makePathRelative')->with(realpath('.'), getcwd())->once()->andReturn('./');
        $this->filesystem->shouldReceive('makePathRelative')->with(getcwd(), realpath('./'))->once()->andReturn('foo');

        $this->assertSame('foo', $this->pathsHelper->getGitHookExecutionPath());
    }

    public function testGetGitHooksDir()
    {
        $this->checker->shouldReceive('getGitDir')->withNoArgs()->once()->andReturn('.');

        $this->filesystem->shouldReceive('exists')->with('.')->once()->andReturn(true);
        $this->filesystem->shouldReceive('makePathRelative')->with(realpath('.'), getcwd())->once()->andReturn('./');

        $this->assertSame('./.git/hooks/', $this->pathsHelper->getGitHooksDir());
    }

    public function testGetGitHookTemplatesDir()
    {
        $this->assertSame('./resources/hooks/', $this->pathsHelper->getGitHookTemplatesDir());
    }

    public function testGetBinDir()
    {
        $this->checker->shouldReceive('getBinDir')->withNoArgs()->once()->andReturn('bin');

        $this->filesystem->shouldReceive('exists')->with('bin')->once()->andReturn(true);
        $this->filesystem->shouldReceive('makePathRelative')->with(realpath('bin'), getcwd())->once()->andReturn('bin');

        $this->assertSame('bin', $this->pathsHelper->getBinDir());
    }

    public function testGetBinDirNotFound()
    {
        $this->setExpectedException(DirectoryNotFoundException::class, sprintf(
            'The configured BIN directory `%s` could not be found.',
            'bin'
        ));

        $this->checker->shouldReceive('getBinDir')->withNoArgs()->once()->andReturn('bin');

        $this->filesystem->shouldReceive('exists')->with('bin')->once()->andReturn(false);
        $this->filesystem->shouldReceive('makePathRelative')->with(realpath('bin'), getcwd())->never();

        $this->pathsHelper->getBinDir();
    }

    public function testGetBinCommand()
    {
        $this->executableFinder->shouldReceive('find')->with('foo', m::any())->once()->andReturn('foo');

        $this->assertSame('foo', $this->pathsHelper->getBinCommand('foo'));
    }

    public function testGetRelativePath()
    {
        $this->filesystem->shouldReceive('makePathRelative')->with(realpath('.'), getcwd())->once()->andReturn('foo');

        $this->assertSame('foo', $this->pathsHelper->getRelativePath('.'));
    }

    public function testGetRelativeProjectPath()
    {
        $this->checker->shouldReceive('getGitDir')->withNoArgs()->twice()->andReturn('.');

        $this->filesystem->shouldReceive('exists')->with('.')->twice()->andReturn(true);
        $this->filesystem->shouldReceive('makePathRelative')->andReturnValues([__DIR__, __DIR__.'/..', __DIR__]);

        $this->assertSame(realpath(__DIR__.'/..'), $this->pathsHelper->getRelativeProjectPath(__DIR__.'/..'));
        $this->assertSame(realpath(__DIR__), $this->pathsHelper->getRelativeProjectPath(__DIR__.'/..'));
    }

    public function testGetAbsolutePath()
    {
        $this->assertSame(realpath('.'), $this->pathsHelper->getAbsolutePath('.'));
    }

    public function testGetAbsolutePathNotFound()
    {
        $this->setExpectedException(FileNotFoundException::class, 'File `foo` was not found.');

        $this->pathsHelper->getAbsolutePath('foo');
    }

    public function testGetPathWithTrailingSlash()
    {
        $this->assertSame('', $this->pathsHelper->getPathWithTrailingSlash(''));
        $this->assertSame('foo'.DIRECTORY_SEPARATOR, $this->pathsHelper->getPathWithTrailingSlash('foo'));
    }

    public function testGetDefaultConfigPath()
    {
        $this->configFile->shouldReceive('getDefaultPath')->withNoArgs()->once()->andReturn('foo');

        $this->assertSame('foo', $this->pathsHelper->getDefaultConfigPath());
    }
}
