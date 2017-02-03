<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Helper\PathsHelper;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Repository\Filesystem;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group  console/command/git
 * @covers \ClickNow\Checker\Console\Command\Git\UninstallCommand
 * @runTestsInSeparateProcesses
 */
class UninstallCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Repository\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

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
        $this->filesystem = m::mock(Filesystem::class);

        $application = new Application();
        $application->add(new UninstallCommand(
            $this->filesystem,
            m::spy(IOInterface::class),
            ['hook1' => [], 'hook2' => []]
        ));

        $this->pathsHelper = m::spy(PathsHelper::class);

        $command = $application->find('git:uninstall');
        $command->getHelperSet()->set($this->pathsHelper, 'paths');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $this->pathsHelper->shouldReceive('getGitHooksDir')->withNoArgs()->once()->andReturn('./');

        $this->filesystem->shouldReceive('exists')->with('./hook1')->once()->andReturn(false);
        $this->filesystem->shouldReceive('exists')->with('./hook1.checker')->once()->andReturn(false);
        $this->filesystem->shouldReceive('remove')->with('./hook1')->never();
        $this->filesystem->shouldReceive('rename')->with('./hook1.checker', './hook1')->never();

        $this->filesystem->shouldReceive('exists')->with('./hook2')->once()->andReturn(true);
        $this->filesystem->shouldReceive('remove')->with('./hook2')->once()->andReturnNull();
        $this->filesystem->shouldReceive('exists')->with('./hook2.checker')->once()->andReturn(true);
        $this->filesystem->shouldReceive('rename')->with('./hook2.checker', './hook2')->once()->andReturnNull();

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
