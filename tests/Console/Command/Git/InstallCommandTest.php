<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Exception\FileNotFoundException;
use ClickNow\Checker\Helper\PathsHelper;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Repository\Filesystem;
use Mockery as m;
use SplFileInfo;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group  console/command/git
 * @covers \ClickNow\Checker\Console\Command\Git\InstallCommand
 * @runTestsInSeparateProcesses
 */
class InstallCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var \Symfony\Component\Process\ProcessBuilder|\Mockery\MockInterface
     */
    protected $processBuilder;

    /**
     * @var \ClickNow\Checker\Helper\PathsHelper|\Mockery\MockInterface
     */
    protected $pathsHelper;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    protected $commandTester;

    protected function setUp()
    {
        $this->checker = m::mock(Checker::class);
        $this->filesystem = m::mock(Filesystem::class);
        $this->processBuilder = m::mock(ProcessBuilder::class);

        $app = new Application();
        $app->add(new InstallCommand(
            $this->checker,
            $this->filesystem,
            m::spy(IOInterface::class),
            $this->processBuilder,
            ['hook1' => [], 'hook2' => [], 'hook3' => []]
        ));

        $this->checker->shouldReceive('getHooksPreset')->withNoArgs()->andReturn('tmp/');
        $this->checker->shouldReceive('getHooksDir')->withNoArgs()->andReturn(null)->byDefault();

        $this->processBuilder->shouldReceive('setArguments')->withAnyArgs()->andReturnNull();
        $this->processBuilder->shouldReceive('getProcess->getCommandLine')->withNoArgs()->andReturn('');

        $this->pathsHelper = m::spy(PathsHelper::class);
        $this->pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn('tmp/');
        $this->pathsHelper->shouldReceive('getGitHookTemplatesDir')->withNoArgs()->andReturn('');
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with('tmp/')->andReturn('tmp/');
        $this->pathsHelper->shouldReceive('getPathWithTrailingSlash')->with(null)->andReturn(null);

        $command = $app->find('git:install');
        $command->getHelperSet()->set($this->pathsHelper, 'paths');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $this->checker->shouldReceive('getHooksDir')->withNoArgs()->times(3)->andReturnValues([null, 'tmp/']);

        $this->filesystem->shouldReceive('exists')->with('tmp/')->once()->andReturn(false);
        $this->filesystem->shouldReceive('mkdir')->with('tmp/')->once()->andReturnNull();

        $this->filesystem->shouldReceive('exists')->with('tmp/hook1')->twice()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with('tmp/hook2')->times(3)->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with('tmp/hook3')->times(3)->andReturnValues([false, true, false]);

        $this->filesystem
            ->shouldReceive('readFromFileInfo')
            ->with(m::type(SplFileInfo::class))
            ->times(5)
            ->andReturnValues(['', InstallCommand::GENERATED_MESSAGE]);

        $this->filesystem->shouldReceive('dumpFile')->with('tmp/hook1', m::any())->once()->andReturnNull();
        $this->filesystem->shouldReceive('dumpFile')->with('tmp/hook2', m::any())->once()->andReturnNull();
        $this->filesystem->shouldReceive('dumpFile')->with('tmp/hook3', m::any())->once()->andReturnNull();

        $this->filesystem->shouldReceive('chmod')->with('tmp/hook1', 0775)->once()->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->with('tmp/hook2', 0775)->once()->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->with('tmp/hook3', 0775)->once()->andReturnNull();

        $this->filesystem->shouldReceive('rename')->with('tmp/hook1', 'tmp/hook1.checker', true)->once();
        $this->filesystem->shouldReceive('rename')->with('tmp/hook2', 'tmp/hook2.checker', true)->never();
        $this->filesystem->shouldReceive('rename')->with('tmp/hook3', 'tmp/hook3.checker', true)->never();

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunWithExoticConfig()
    {
        $this->pathsHelper->shouldReceive('getAbsolutePath')->with('foo')->andReturnValues(['foo', 'bar']);
        $this->pathsHelper->shouldReceive('getDefaultConfigPath')->withNoArgs()->andReturn('bar');
        $this->pathsHelper->shouldReceive('getRelativeProjectPath')->with('foo')->once()->andReturn('foo');

        $this->filesystem->shouldReceive('exists')->with('tmp/')->once()->andReturn(true);

        $this->filesystem->shouldReceive('exists')->with('tmp/hook1')->twice()->andReturnValues([true, false]);
        $this->filesystem->shouldReceive('exists')->with('tmp/hook2')->twice()->andReturnValues([true, false]);
        $this->filesystem->shouldReceive('exists')->with('tmp/hook3')->twice()->andReturnValues([true, false]);

        $this->filesystem
            ->shouldReceive('readFromFileInfo')
            ->with(m::type(SplFileInfo::class))
            ->times(3)
            ->andReturn('');

        $this->filesystem->shouldReceive('dumpFile')->with('tmp/hook1', m::any())->once()->andReturnNull();
        $this->filesystem->shouldReceive('dumpFile')->with('tmp/hook2', m::any())->once()->andReturnNull();
        $this->filesystem->shouldReceive('dumpFile')->with('tmp/hook3', m::any())->once()->andReturnNull();

        $this->filesystem->shouldReceive('chmod')->with('tmp/hook1', 0775)->once()->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->with('tmp/hook2', 0775)->once()->andReturnNull();
        $this->filesystem->shouldReceive('chmod')->with('tmp/hook3', 0775)->once()->andReturnNull();

        $this->processBuilder->shouldReceive('add')->with('--config=foo')->once()->andReturnNull();

        $this->commandTester->execute([
            '--config' => 'foo',
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testHookTemplateNotFound()
    {
        $this->setExpectedException(FileNotFoundException::class);

        $this->filesystem->shouldReceive('exists')->with('tmp/')->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with(m::not('tmp/'))->andReturn(false);

        $this->commandTester->execute([]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }
}
