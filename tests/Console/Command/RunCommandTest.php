<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Context\RunContext;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Helper\RunnerHelper;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Git;
use ClickNow\Checker\Runner\CommandsCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group  console/command
 * @covers \ClickNow\Checker\Console\Command\RunCommand
 * @covers \ClickNow\Checker\Console\Command\AbstractRunnerCommand
 * @runTestsInSeparateProcesses
 */
class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface|\Mockery\MockInterface
     */
    protected $runner;

    /**
     * @var \ClickNow\Checker\Runner\CommandsCollection
     */
    protected $commandsCollection;

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
        $this->runner = m::mock(RunnerInterface::class);

        $this->commandsCollection = new CommandsCollection();
        $this->git = m::mock(Git::class);

        $application = new Application();
        $application->add(new RunCommand($this->commandsCollection, $this->git));

        $this->runnerHelper = m::spy(RunnerHelper::class);

        $command = $application->find('run');
        $command->getHelperSet()->set($this->runnerHelper, 'runner');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $this->commandsCollection->set('foo', $this->runner);
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(RunContext::class))->once()->andReturn(0);

        $this->commandTester->execute(['name' => 'foo']);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunAndReturnError()
    {
        $this->commandsCollection->set('foo', $this->runner);
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(RunContext::class))->once()->andReturn(1);

        $this->commandTester->execute(['name' => 'foo']);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testRunWithOptions()
    {
        $this->commandsCollection->set('foo', $this->runner);
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(RunContext::class))->once()->andReturn(0);

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
            'name'                         => 'foo',
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

    public function testCommandNotFound()
    {
        $this->setExpectedException(CommandNotFoundException::class, 'Command `foo` was not found.');

        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->never();

        $this->commandTester->execute(['name' => 'foo']);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testCommandInvalid()
    {
        $this->setExpectedException(CommandInvalidException::class, 'Command `foo` must implement RunnerInterface.');

        $this->commandsCollection->set('foo', 'bar');
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->never();

        $this->commandTester->execute(['name' => 'foo']);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }
}
