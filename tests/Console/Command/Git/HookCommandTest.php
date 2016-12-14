<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Console\Helper\RunnerHelper;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Git;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group console/command
 * @covers \ClickNow\Checker\Console\Command\Git\HookCommand
 * @runTestsInSeparateProcesses
 */
class HookCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Repository\Git|\Mockery\MockInterface
     */
    protected $git;

    /**
     * @var \ClickNow\Checker\Console\Helper\RunnerHelper|\Mockery\MockInterface
     */
    protected $runnerHelper;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    protected $commandTester;

    protected function setUp()
    {
        if (!defined('STDIN')) {
            define('STDIN', null);
        }

        $this->git = m::mock(Git::class);

        $hookCommand = m::spy(CommandInterface::class);
        $hookCommand->shouldReceive('getName')->withNoArgs()->andReturn('foo');

        $application = new Application();
        $application->add(new HookCommand($hookCommand, $this->git));

        $this->runnerHelper = m::spy(RunnerHelper::class);

        $command = $application->find('git:foo');
        $command->getHelperSet()->set($this->runnerHelper, 'runner');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(ContextInterface::class))->once()->andReturn(0);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunAndReturnError()
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(ContextInterface::class))->once()->andReturn(1);

        $this->commandTester->execute([]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testRunWithOptions()
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(ContextInterface::class))->once()->andReturn(0);

        $this->commandTester->execute([
            '--process-timeout'         => 10,
            '--stop-on-failure'         => true,
            '--ignore-unstaged-changes' => true,
            '--skip-success-output'     => true,
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
