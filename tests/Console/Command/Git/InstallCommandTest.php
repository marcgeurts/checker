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
        $this->processBuilder = m::mock(ProcessBuilder::class);

        $app = new Application();
        $app->add(
            new InstallCommand($this->checker, $this->filesystem, m::spy(IOInterface::class), $this->processBuilder)
        );

        $this->checker->shouldReceive('getHooksPreset')->withNoArgs()->andReturn($this->tmpDir);
        $this->checker->shouldReceive('getHooksDir')->withNoArgs()->andReturn(null)->byDefault();

        $this->processBuilder->shouldReceive('setArguments')->withAnyArgs()->andReturnNull();
        $this->processBuilder->shouldReceive('getProcess->getCommandLine')->withNoArgs()->andReturn('');

        $this->pathsHelper = m::spy(PathsHelper::class);
        $this->pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn($this->tmpDir);
        $this->pathsHelper->shouldReceive('getGitHookTemplatesDir')->withNoArgs()->andReturn('');
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with($this->tmpDir)->andReturn($this->tmpDir);
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with(null)->andReturn($this->tmpDir.'/invalid');

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
        file_put_contents($this->tmpDir.'pre-commit', '');
        file_put_contents($this->tmpDir.'pre-push', InstallCommand::GENERATED_MESSAGE);

        $this->checker->shouldReceive('getHooksDir')->withNoArgs()->andReturnValues([null, $this->tmpDir]);

        $this->filesystem->shouldReceive('exists')->with($this->tmpDir)->once()->andReturn(false);
        $this->filesystem->shouldReceive('mkdir')->with($this->tmpDir)->once()->andReturnNull();
        $this->filesystem->shouldReceive('exists')->with($this->tmpDir.'all')->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($this->tmpDir.'pre-commit')->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($this->tmpDir.'pre-push')->andReturn(true);
        $this->filesystem->shouldReceive('exists')->withAnyArgs()->andReturn(false);
        $this->filesystem->shouldReceive('dumpFile')->withAnyArgs()->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->withAnyArgs()->andReturnNull();

        $this->filesystem
            ->shouldReceive('rename')
            ->with($this->tmpDir.'pre-commit', $this->tmpDir.'pre-commit.checker', true)
            ->once()
            ->andReturnNull();

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunWithExoticConfig()
    {
        file_put_contents($this->tmpDir.'all', '');

        $this->pathsHelper->shouldReceive('getAbsolutePath')->with('foo')->andReturnValues(['foo', 'bar']);
        $this->pathsHelper->shouldReceive('getDefaultConfigPath')->withNoArgs()->andReturn('bar');
        $this->pathsHelper->shouldReceive('getRelativeProjectPath')->with('foo')->once()->andReturn('foo');

        $this->filesystem->shouldReceive('exists')->with($this->tmpDir)->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with($this->tmpDir.'all')->andReturn(true);
        $this->filesystem->shouldReceive('exists')->withAnyArgs()->andReturn(false);
        $this->filesystem->shouldReceive('dumpFile')->withAnyArgs()->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->withAnyArgs()->andReturnNull();

        $this->processBuilder->shouldReceive('add')->with('--config=foo')->once()->andReturnNull();

        $this->commandTester->execute([
            '--config' => 'foo',
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testHookTemplateNotFound()
    {
        $this->setExpectedException(FileNotFoundException::class);

        $this->filesystem->shouldReceive('exists')->with($this->tmpDir)->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with(m::not($this->tmpDir))->andReturn(false);

        $this->commandTester->execute([]);
    }
}
