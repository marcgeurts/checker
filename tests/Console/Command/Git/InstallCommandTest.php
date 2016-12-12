<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Console\Helper\PathsHelper;
use ClickNow\Checker\IO\IOInterface;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group console/command
 * @covers \ClickNow\Checker\Console\Command\Git\InstallCommand
 */
class InstallCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $tmpDir;

    /**
     * @var \ClickNow\Checker\Config\Checker|\Mockery\MockInterface
     */
    protected $checker;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \Symfony\Component\Process\ProcessBuilder|\Mockery\MockInterface
     */
    protected $processBuilder;

    /**
     * @var \ClickNow\Checker\Console\Helper\PathsHelper|\Mockery\MockInterface
     */
    protected $pathsHelper;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    protected $commandTester;

    protected function setUp()
    {
        $this->tmpDir = __DIR__ . '/tmp/';

        $fs = new Filesystem();
        $fs->mkdir($this->tmpDir);

        $this->checker = m::mock(Checker::class);
        $this->filesystem = m::mock(Filesystem::class);
        $this->io = m::mock(IOInterface::class);
        $this->processBuilder = m::mock(ProcessBuilder::class);

        $app = new Application();
        $app->add(new InstallCommand($this->checker, $this->filesystem, $this->io, $this->processBuilder));

        $this->pathsHelper = m::spy(PathsHelper::class);

        $command = $app->find('git:install');
        $command->getHelperSet()->set($this->pathsHelper, 'paths');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();

        $fs = new Filesystem();
        $fs->remove($this->tmpDir);
    }

    public function testRun()
    {
        file_put_contents($this->tmpDir.'all', '');

        $this->checker->shouldReceive('getHooksPreset')->withNoArgs()->andReturn($this->tmpDir);
        $this->checker->shouldReceive('getHooksDir')->withNoArgs()->andReturnNull();

        $this->pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn($this->tmpDir);
        $this->pathsHelper->shouldReceive('getGitHookTemplatesDir')->withNoArgs()->andReturn('');
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with($this->tmpDir)->andReturn($this->tmpDir);
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with(null)->andReturn(false);

        $this->filesystem->shouldReceive('exists')->with($this->tmpDir)->once()->andReturn(false);
        $this->filesystem->shouldReceive('mkdir')->with($this->tmpDir)->once()->andReturnNull();
        $this->filesystem->shouldReceive('exists')->with(m::not($this->tmpDir.'all'))->andReturn(false);
        $this->filesystem->shouldReceive('exists')->with($this->tmpDir.'all')->andReturn(true);
        $this->filesystem->shouldReceive('dumpFile')->with(m::not($this->tmpDir.'all'), '')->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->with(m::not($this->tmpDir.'all'), 0775)->andReturnNull();

        $this->io->shouldReceive('title')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('log')->withAnyArgs()->andReturnNull();
        $this->io->shouldReceive('note')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('success')->withAnyArgs()->once()->andReturnNull();

        $this->processBuilder->shouldReceive('setArguments')->withAnyArgs()->andReturnNull();
        $this->processBuilder->shouldReceive('getProcess->getCommandLine')->withNoArgs()->andReturn('');

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}