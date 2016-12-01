<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Console\Application;
use ClickNow\Checker\Console\Helper\RunnerHelper;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Git;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group console/command/git
 * @covers \ClickNow\Checker\Console\Command\Git\HookCommand
 */
class HookCommandTest extends \PHPUnit_Framework_TestCase
{
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
        $this->git = m::mock(Git::class);

        $hookCommand = m::mock(CommandInterface::class);
        $hookCommand->shouldReceive('getName')->withNoArgs()->andReturn('foo');
        $hookCommand->shouldReceive('setConfig')->withAnyArgs()->andReturnNull();

        $app = new Application();
        $app->add(new HookCommand($hookCommand, $this->git));

        $runner = $this->getMock(RunnerHelper::class, [], [], '', false);

        $command = $app->find('git:foo');
        $command->getHelperSet()->set($runner, 'runner');

        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testRun()
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testRunWithOptions()
    {
        $this->git->shouldReceive('getChangedFiles')->with(null)->once()->andReturn(new FilesCollection());

        $this->commandTester->execute([
            '--process-timeout'         => 10,
            '--stop-on-failure'         => true,
            '--ignore-unstaged-changes' => true,
            '--skip-success-output'     => true,
        ]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }
}
