<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Context\Git\CommitMsgContext;
use ClickNow\Checker\Helper\RunnerHelper;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Filesystem;
use ClickNow\Checker\Repository\Git;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group  console/command/git
 * @covers \ClickNow\Checker\Console\Command\Git\CommitMsgCommand
 * @covers \ClickNow\Checker\Console\Command\AbstractRunnerCommand
 * @runTestsInSeparateProcesses
 */
class CommitMsgCommandTest extends \PHPUnit_Framework_TestCase
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
     * @var \ClickNow\Checker\Repository\Filesystem|\Mockery\MockInterface
     */
    protected $filesystem;

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
        $this->filesystem = m::mock(Filesystem::class);

        $application = new Application();
        $application->add(new CommitMsgCommand($this->runner, $this->git, $this->filesystem));

        $this->runnerHelper = m::spy(RunnerHelper::class);

        $command = $application->find('git:commit-msg');
        $command->getHelperSet()->set($this->runnerHelper, 'runner');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $this->git->shouldReceive('getCommitMessage')->withNoArgs()->once()->andReturn('foo');
        $this->git->shouldReceive('getUserName')->withNoArgs()->once()->andReturn('bar');
        $this->git->shouldReceive('getUserEmail')->withNoArgs()->once()->andReturn('foo@bar');
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());

        $this->runnerHelper->shouldReceive('run')->with(m::type(CommitMsgContext::class))->once()->andReturn(0);

        $this->filesystem->shouldReceive('exists')->with('')->once()->andReturn(false);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunAndReturnError()
    {
        $this->git->shouldReceive('getCommitMessage')->withNoArgs()->once()->andReturn('foo');
        $this->git->shouldReceive('getUserName')->withNoArgs()->once()->andReturn('bar');
        $this->git->shouldReceive('getUserEmail')->withNoArgs()->once()->andReturn('foo@bar');
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());

        $this->runnerHelper->shouldReceive('run')->with(m::type(CommitMsgContext::class))->once()->andReturn(1);

        $this->filesystem->shouldReceive('exists')->with('')->once()->andReturn(false);

        $this->commandTester->execute([]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testRunWithOptions()
    {
        $this->git->shouldReceive('getCommitMessage')->withNoArgs()->never();
        $this->git->shouldReceive('getUserName')->withNoArgs()->never();
        $this->git->shouldReceive('getUserEmail')->withNoArgs()->never();
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());

        $this->runnerHelper->shouldReceive('run')->with(m::type(CommitMsgContext::class))->once()->andReturn(0);

        $type = m::type(\SplFileInfo::class);
        $this->filesystem->shouldReceive('exists')->with('foo')->once()->andReturn(true);
        $this->filesystem->shouldReceive('readFromFileInfo')->with($type)->once()->andReturn('bar');

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
        $this->runner->shouldReceive('setSkipSuccessOutput')->with(true)->once()->andReturnNull();
        $this->runner->shouldReceive('setSkipSuccessOutput')->with(false)->once()->andReturnNull();

        $this->commandTester->execute([
            'commit-message-file'          => 'foo',
            '--git-user-name'              => 'bar',
            '--git-user-email'             => 'foo@bar',
            '--process-timeout'            => 60,
            '--process-async-wait'         => 1000,
            '--process-async-limit'        => 10,
            '--stop-on-failure'            => true,
            '--no-stop-on-failure'         => true,
            '--ignore-unstaged-changes'    => true,
            '--no-ignore-unstaged-changes' => true,
            '--strict'                     => true,
            '--no-strict'                  => true,
            '--progress'                   => 'bar',
            '--no-progress'                => true,
            '--skip-success-output'        => true,
            '--no-skip-success-output'     => true,
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
