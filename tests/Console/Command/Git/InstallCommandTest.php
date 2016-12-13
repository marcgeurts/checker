<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Console\Helper\PathsHelper;
use ClickNow\Checker\Exception\FileNotFoundException;
use ClickNow\Checker\IO\IOInterface;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group console/commandi
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
        $this->tmpDir = __DIR__.'/tmp/';

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
        $dir = $this->tmpDir;

        file_put_contents($dir.'all', '');
        file_put_contents($dir.'pre-commit', '');
        file_put_contents($dir.'pre-push', InstallCommand::GENERATED_MESSAGE);

        $this->checker->shouldReceive('getHooksPreset')->withNoArgs()->andReturn($dir);
        $this->checker->shouldReceive('getHooksDir')->withNoArgs()->andReturnValues([null, $dir]);

        $this->pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn($dir);
        $this->pathsHelper->shouldReceive('getGitHookTemplatesDir')->withNoArgs()->andReturn('');
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with($dir)->andReturn($dir);
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with(null)->andReturn(false);
        $this->pathsHelper->shouldReceive('getAbsolutePath')->with('foo')->andReturnValues(['foo', 'bar']);
        $this->pathsHelper->shouldReceive('getDefaultConfigPath')->withNoArgs()->andReturn('bar');
        $this->pathsHelper->shouldReceive('getRelativeProjectPath')->with('foo')->once()->andReturn('foo');

        $this->filesystem->shouldReceive('exists')->with($dir)->once()->andReturn(false);
        $this->filesystem->shouldReceive('mkdir')->with($dir)->once()->andReturnNull();

        $this->filesystem->shouldReceive('exists')->with($dir.'all')->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($dir.'pre-commit')->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($dir.'pre-push')->andReturn(true);
        $this->filesystem->shouldReceive('exists')->withAnyArgs()->andReturn(false);
        $this->filesystem->shouldReceive('rename')->with($dir.'pre-commit', $dir.'pre-commit.checker', true)->once();
        $this->filesystem->shouldReceive('dumpFile')->withAnyArgs()->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->withAnyArgs()->andReturnNull();

        $this->io->shouldReceive('title')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('log')->withAnyArgs()->andReturnNull();
        $this->io->shouldReceive('note')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('success')->withAnyArgs()->once()->andReturnNull();

        $this->processBuilder->shouldReceive('setArguments')->withAnyArgs()->andReturnNull();
        $this->processBuilder->shouldReceive('add')->with('--config=foo')->once()->andReturnNull();
        $this->processBuilder->shouldReceive('getProcess->getCommandLine')->withNoArgs()->andReturn('');

        $this->commandTester->execute([
            '--config' => 'foo'
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testHookTemplateNotFound()
    {
        $this->setExpectedException(FileNotFoundException::class);

        $dir = $this->tmpDir;

        $this->checker->shouldReceive('getHooksPreset')->withNoArgs()->once()->andReturn($dir);
        $this->checker->shouldReceive('getHooksDir')->withNoArgs()->once()->andReturn(null);

        $this->pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn($dir);
        $this->pathsHelper->shouldReceive('getGitHookTemplatesDir')->withNoArgs()->once()->andReturn('');
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with($dir)->once()->andReturn($dir);
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with(null)->once()->andReturn(false);

        $this->filesystem->shouldReceive('exists')->with($dir)->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with(m::not($dir))->twice()->andReturn(false);

        $this->io->shouldReceive('title')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('success')->withAnyArgs()->never();

        $this->commandTester->execute([]);
    }
}