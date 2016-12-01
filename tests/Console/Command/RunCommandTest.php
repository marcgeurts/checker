<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Command\CommandsCollection;
use ClickNow\Checker\Console\Helper\RunnerHelper;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Git;
use Mockery as m;
use Symfony\Component\Console\Application;
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
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    protected $commandTester;

    protected function setUp()
    {
        /*$this->commandsCollection = new CommandsCollection();
        $this->git = m::mock(Git::class);

        $app = new Application();
        $app->add(new RunCommand($this->commandsCollection, $this->git));

        $command = $app->find('run');
        $command->getHelperSet()->set(m::spy(RunnerHelper::class), 'runner');

        $this->commandTester = new CommandTester($command);*/
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $commandsCollection = new CommandsCollection();
        $git = m::mock(Git::class);

        $commandsCollection->set('foo', m::spy(CommandInterface::class));
        $git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $app = new Application();
        $app->add(new RunCommand($commandsCollection, $git));

        $runner = $this->getMock(RunnerHelper::class, ['run'], [], '', false);
        $runner->expects($this->once())->method('run')->willReturn(0);

        $command = $app->find('run');
        $command->getHelperSet()->set($runner, 'runner');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['name' => 'foo']);



        /*$this->commandsCollection->set('foo', m::spy(CommandInterface::class));
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $this->commandTester->execute(['name' => 'foo']);

        $this->assertSame(0, $this->commandTester->getStatusCode());*/
    }

    public function testRunWithOptions()
    {
        /*$this->commandsCollection->set('foo', m::spy(CommandInterface::class));
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->once()->andReturn(new FilesCollection());

        $this->commandTester->execute([
            'name'                      => 'foo',
            '--process-timeout'         => 10,
            '--stop-on-failure'         => true,
            '--ignore-unstaged-changes' => true,
            '--skip-success-output'     => true,
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());*/
    }

    public function testRunCommandNotFound()
    {
        /*$this->setExpectedException(CommandNotFoundException::class, 'Command `foo` was not found.');

        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->never();

        $this->commandTester->execute(['name' => 'foo']);*/
    }

    public function testRunCommandInvalid()
    {
        /*$this->setExpectedException(CommandInvalidException::class, 'Command `foo` must implement CommandInterface.');

        $this->commandsCollection->set('foo', 'bar');
        $this->git->shouldReceive('getRegisteredFiles')->withNoArgs()->never();

        $this->commandTester->execute(['name' => 'foo']);*/
    }
}
