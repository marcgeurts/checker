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
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    protected $commandTester;
    
    protected function setUp()
    {
        $this->filesystem = m::spy(Filesystem::class);

        $app = new Application();
        $app->add(new UninstallCommand($this->filesystem, m::spy(IOInterface::class)));

        $pathsHelper = m::spy(PathsHelper::class);
        $pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn('');

        $command = $app->find('git:uninstall');
        $command->getHelperSet()->set($pathsHelper, 'paths');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $this->filesystem->shouldReceive('exists')->with('pre-commit')->once()->andReturn(true);
        $this->filesystem->shouldReceive('exists')->with('pre-commit.checker')->once()->andReturn(true);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}