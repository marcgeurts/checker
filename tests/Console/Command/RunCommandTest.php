<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Command\CommandsCollection;
use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Console\Helper\RunnerHelper;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Git;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group console/command
 * @covers \ClickNow\Checker\Console\Command\RunCommand
 */
class RunCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\CommandsCollection
     */
    protected $commandsCollection;

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
        $this->commandsCollection->set('foo', m::spy(CommandInterface::class));
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(ContextInterface::class))->once()->andReturn(0);

        $this->commandTester->execute(['name' => 'foo']);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunAndReturnError()
    {
        $this->commandsCollection->set('foo', m::spy(CommandInterface::class));
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(ContextInterface::class))->once()->andReturn(1);

        $this->commandTester->execute(['name' => 'foo']);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testRunWithOptions()
    {
        $this->commandsCollection->set('foo', m::spy(CommandInterface::class));
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());
        $this->runnerHelper->shouldReceive('run')->with(m::type(ContextInterface::class))->once()->andReturn(0);

        $this->commandTester->execute([
            'name'                      => 'foo',
            '--process-timeout'         => 10,
            '--stop-on-failure'         => true,
            '--ignore-unstaged-changes' => true,
            '--skip-success-output'     => true,
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testCommandNotFound()
    {
        $this->setExpectedException(CommandNotFoundException::class, 'Command `foo` was not found.');

        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->never();

        $this->commandTester->execute(['name' => 'foo']);
    }

    public function testCommandInvalid()
    {
        $this->setExpectedException(CommandInvalidException::class, 'Command `foo` must implement CommandInterface.');

        $this->commandsCollection->set('foo', 'bar');
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->never();

        $this->commandTester->execute(['name' => 'foo']);
    }
}
