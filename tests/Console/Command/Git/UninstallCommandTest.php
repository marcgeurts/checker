<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Console\Helper\PathsHelper;
use ClickNow\Checker\IO\IOInterface;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group console/command
 * @covers \ClickNow\Checker\Console\Command\Git\UninstallCommand
 */
class UninstallCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

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
        $this->filesystem = m::mock(Filesystem::class);
        $this->io = m::mock(IOInterface::class);

        $app = new Application();
        $app->add(new UninstallCommand($this->filesystem, $this->io));

        $this->pathsHelper = m::spy(PathsHelper::class);

        $command = $app->find('git:uninstall');
        $command->getHelperSet()->set($this->pathsHelper, 'paths');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $path = './';
        $hook = $path.'pre-commit';

        $this->pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn($path);

        $this->filesystem->shouldReceive('exists')->with(m::notAnyOf($hook, $hook.'.checker'))->andReturn(false);
        $this->filesystem->shouldReceive('exists')->with($hook)->once()->andReturn(true);
        $this->filesystem->shouldReceive('remove')->with($hook)->once()->andReturnNull();
        $this->filesystem->shouldReceive('exists')->with($hook.'.checker')->once()->andReturn(true);
        $this->filesystem->shouldReceive('rename')->with($hook.'.checker', $hook)->once()->andReturnNull();

        $this->io->shouldReceive('title')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('log')->withAnyArgs()->twice()->andReturnNull();
        $this->io->shouldReceive('note')->withAnyArgs()->once()->andReturnNull();
        $this->io->shouldReceive('success')->withAnyArgs()->once()->andReturnNull();

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}