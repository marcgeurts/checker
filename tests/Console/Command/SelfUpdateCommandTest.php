<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\Console\Application;
use ClickNow\Checker\IO\IOInterface;
use Exception;
use Humbug\SelfUpdate\Strategy\GithubStrategy;
use Humbug\SelfUpdate\Updater;
use Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group  console/command
 * @covers \ClickNow\Checker\Console\Command\SelfUpdateCommand
 * @runTestsInSeparateProcesses
 */
class SelfUpdateCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface|\Mockery\MockInterface
     */
    protected $io;

    /**
     * @var \Humbug\SelfUpdate\Updater|\Mockery\MockInterface
     */
    protected $updater;

    /**
     * @var \Symfony\Component\Console\Tester\CommandTester
     */
    protected $commandTester;

    protected function setUp()
    {
        $this->io = m::mock(IOInterface::class);
        $this->io->shouldReceive('title')->once()->withAnyArgs()->andReturnNull();

        $this->updater = m::mock(Updater::class);
        $this->updater->shouldReceive('getStrategy')->once()->withNoArgs()->andReturn(m::spy(GithubStrategy::class));

        $application = new Application();
        $application->add(new SelfUpdateCommand($this->io, $this->updater));

        $this->commandTester = new CommandTester($application->find('self-update'));
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testUpdateSuccessfully()
    {
        $this->io->shouldReceive('success')->once()->withAnyArgs()->andReturnNull();
        $this->io->shouldReceive('note')->never();
        $this->io->shouldReceive('error')->never();

        $this->updater->shouldReceive('update')->once()->withNoArgs()->andReturn(true);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testUpdateNoNeed()
    {
        $this->io->shouldReceive('success')->never();
        $this->io->shouldReceive('note')->once()->withAnyArgs()->andReturnNull();
        $this->io->shouldReceive('error')->never();

        $this->updater->shouldReceive('update')->once()->withNoArgs()->andReturn(false);

        $this->commandTester->execute([]);

        $this->assertSame(0, $this->commandTester->getStatusCode());
    }

    public function testUpdateError()
    {
        $this->io->shouldReceive('success')->never();
        $this->io->shouldReceive('note')->never();
        $this->io->shouldReceive('error')->once()->with('ERROR')->andReturnNull();

        $this->updater->shouldReceive('update')->once()->withNoArgs()->andThrow(Exception::class, 'ERROR');

        $this->commandTester->execute([]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }
}
