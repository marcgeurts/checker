<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Context\Git\PreCommitContext;
use ClickNow\Checker\Helper\RunnerHelper;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Git;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group  console/command/git
 * @covers \ClickNow\Checker\Console\Command\Git\PreCommitCommand
 * @covers \ClickNow\Checker\Console\Command\AbstractRunnerCommand
 * @runTestsInSeparateProcesses
 */
class PreCommitCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface|\Mockery\MockInterface
     */
    protected $runner;

    /**
     * @var \ClickNow\Checker\Repository\Git|\Mockery\MockInterface
     */
    protected $git;

    /**
     * @var \ClickNow\Checker\Helper\RunnerHelper|\Mockery\MockInterface
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

        $this->runner = m::mock(RunnerInterface::class);
        $this->git = m::mock(Git::class);

        $application = new Application();
        $application->add(new PreCommitCommand($this->runner, $this->git));

        $this->runnerHelper = m::spy(RunnerHelper::class);

        $command = $application->find('git:pre-commit');
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
        $this->runnerHelper->shouldReceive('run')->with(m::type(PreCommitContext::class))->once()->andReturn(0);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunAndReturnError()
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(PreCommitContext::class))->once()->andReturn(1);

        $this->commandTester->execute([]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testRunWithOptions()
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(PreCommitContext::class))->once()->andReturn(0);

        $this->runner->shouldReceive('setProcessTimeout')->with(60)->once()->andReturnNull();
        $this->runner->shouldReceive('setProcessAsyncWait')->with(1000)->once()->andReturnNull();
        $this->runner->shouldReceive('setProcessAsyncLimit')->with(10)->once()->andReturnNull();
        $this->runner->shouldReceive('setStopOnFailure')->with(true)->once()->andReturnNull();
        $this->runner->shouldReceive('setStopOnFailure')->with(false)->once()->andReturnNull();
        $this->runner->shouldReceive('setIgnoreUnstagedChanges')->with(true)->once()->andReturnNull();
        $this->runner->shouldReceive('setIgnoreUnstagedChanges')->with(false)->once()->andReturnNull();
        $this->runner->shouldReceive('setStrict')->with(true)->once()->andReturnNull();
        $this->runner->shouldReceive('setStrict')->with(false)->once()->andReturnNull();
        $this->runner->shouldReceive('setProgress')->with('bar')->once()->andReturnNull();
        $this->runner->shouldReceive('setProgress')->with(null)->once()->andReturnNull();
        $this->runner->shouldReceive('setSkipEmptyOutput')->with(true)->once()->andReturnNull();
        $this->runner->shouldReceive('setSkipEmptyOutput')->with(false)->once()->andReturnNull();
        $this->runner->shouldReceive('setSkipSuccessOutput')->with(true)->once()->andReturnNull();
        $this->runner->shouldReceive('setSkipSuccessOutput')->with(false)->once()->andReturnNull();
        $this->runner->shouldReceive('setSkipCircumventionOutput')->with(true)->once()->andReturnNull();
        $this->runner->shouldReceive('setSkipCircumventionOutput')->with(false)->once()->andReturnNull();

        $this->commandTester->execute([
            '--process-timeout'              => 60,
            '--process-async-wait'           => 1000,
            '--process-async-limit'          => 10,
            '--stop-on-failure'              => true,
            '--no-stop-on-failure'           => true,
            '--ignore-unstaged-changes'      => true,
            '--no-ignore-unstaged-changes'   => true,
            '--strict'                       => true,
            '--no-strict'                    => true,
            '--progress'                     => 'bar',
            '--no-progress'                  => true,
            '--skip-empty-output'            => true,
            '--no-skip-empty-output'         => true,
            '--skip-success-output'          => true,
            '--no-skip-success-output'       => true,
            '--skip-circumvention-output'    => true,
            '--no-skip-circumvention-output' => true,
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
