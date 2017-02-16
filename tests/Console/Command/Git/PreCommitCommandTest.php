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

    /**
     * @dataProvider options
     */
    public function testRunWithOptions($option, $function, $valueFunction, $valueOption)
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(PreCommitContext::class))->once()->andReturn(0);

        $this->runner->shouldReceive($function)->with($valueFunction)->once()->andReturnNull();

        $this->commandTester->execute(['--'.$option => $valueOption]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function options()
    {
        return [
            ['process-timeout', 'setProcessTimeout', 60, 60],
            ['process-async-wait', 'setProcessAsyncWait', 1000, 1000],
            ['process-async-limit', 'setProcessAsyncLimit', 10, 10],
            ['stop-on-failure', 'setStopOnFailure', true, true],
            ['no-stop-on-failure', 'setStopOnFailure', false, true],
            ['ignore-unstaged-changes', 'setIgnoreUnstagedChanges', true, true],
            ['no-ignore-unstaged-changes', 'setIgnoreUnstagedChanges', false, true],
            ['strict', 'setStrict', true, true],
            ['no-strict', 'setStrict', false, true],
            ['progress', 'setProgress', 'bar', 'bar'],
            ['no-progress', 'setProgress', null, true],
            ['skip-success-output', 'setSkipSuccessOutput', true, true],
            ['no-skip-success-output', 'setSkipSuccessOutput', false, true],
        ];
    }
}
