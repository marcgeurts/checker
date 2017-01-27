<?php

namespace ClickNow\Checker\Composer;

use Composer\Composer;
use Composer\Config;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UninstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Mockery as m;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group  composer
 * @covers \ClickNow\Checker\Composer\CheckerPlugin
 * @runTestsInSeparateProcesses
 */
class CheckerPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Composer\CheckerPlugin
     */
    protected $checkerPlugin;

    protected function setUp()
    {
        $this->checkerPlugin = new CheckerPlugin();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(PluginInterface::class, $this->checkerPlugin);
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->checkerPlugin);
    }

    public function testActivate()
    {
        $this->checkerPlugin->activate(m::mock(Composer::class), m::mock(IOInterface::class));
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', CheckerPlugin::getSubscribedEvents());
        $this->assertCount(5, CheckerPlugin::getSubscribedEvents());
    }

    public function testPostPackageInstallSuccessfully()
    {
        $operation = m::mock(InstallOperation::class);
        $operation->shouldReceive('getPackage')->withNoArgs()->once()->andReturn($this->mockPackage());

        $this->activateSuccess();
        $this->checkerPlugin->postPackageInstall($this->mockEvent($operation));
        $this->checkerPlugin->runScheduledTasks();
    }

    public function testPostPackageInstallFail()
    {
        $operation = m::mock(InstallOperation::class);
        $operation->shouldReceive('getPackage')->withNoArgs()->once()->andReturn($this->mockPackage());

        $this->activateFail();
        $this->checkerPlugin->postPackageInstall($this->mockEvent($operation));
        $this->checkerPlugin->runScheduledTasks();
    }

    public function testPostPackageInstallDisabled()
    {
        $package = $this->mockPackage();
        $package->shouldReceive('getName')->withNoArgs()->once()->andReturnNull();

        $operation = m::mock(InstallOperation::class);
        $operation->shouldReceive('getPackage')->withNoArgs()->once()->andReturn($package);

        $this->checkerPlugin->postPackageInstall($this->mockEvent($operation));
    }

    public function testPostPackageUpdateSuccessfully()
    {
        $operation = m::mock(UpdateOperation::class);
        $operation->shouldReceive('getTargetPackage')->withNoArgs()->once()->andReturn($this->mockPackage());

        $this->activateSuccess();
        $this->checkerPlugin->postPackageUpdate($this->mockEvent($operation));
        $this->checkerPlugin->runScheduledTasks();
    }

    public function testPostPackageUpdateFail()
    {
        $operation = m::mock(UpdateOperation::class);
        $operation->shouldReceive('getTargetPackage')->withNoArgs()->once()->andReturn($this->mockPackage());

        $this->activateFail();
        $this->checkerPlugin->postPackageUpdate($this->mockEvent($operation));
        $this->checkerPlugin->runScheduledTasks();
    }

    public function testPostPackageUpdateDisabled()
    {
        $package = $this->mockPackage();
        $package->shouldReceive('getName')->withNoArgs()->once()->andReturnNull();

        $operation = m::mock(UpdateOperation::class);
        $operation->shouldReceive('getTargetPackage')->withNoArgs()->once()->andReturn($package);

        $this->checkerPlugin->postPackageUpdate($this->mockEvent($operation));
    }

    public function testPrePackageUninstallSuccessfully()
    {
        $operation = m::mock(UninstallOperation::class);
        $operation->shouldReceive('getPackage')->withNoArgs()->once()->andReturn($this->mockPackage());

        $this->activateSuccess();
        $this->checkerPlugin->prePackageUninstall($this->mockEvent($operation));
    }

    public function testPrePackageUninstallFail()
    {
        $operation = m::mock(UninstallOperation::class);
        $operation->shouldReceive('getPackage')->withNoArgs()->once()->andReturn($this->mockPackage());

        $this->activateFail();
        $this->checkerPlugin->prePackageUninstall($this->mockEvent($operation));
    }

    public function testPrePackageUninstallDisabled()
    {
        $package = $this->mockPackage();
        $package->shouldReceive('getName')->withNoArgs()->once()->andReturnNull();

        $operation = m::mock(UninstallOperation::class);
        $operation->shouldReceive('getPackage')->withNoArgs()->once()->andReturn($package);

        $this->checkerPlugin->prePackageUninstall($this->mockEvent($operation));
    }

    /**
     * Activate success.
     *
     * @return void
     */
    protected function activateSuccess()
    {
        $process = $this->mockProcess();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(true);
        $process->shouldReceive('getOutput')->withNoArgs()->once()->andReturn('bar');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->withNoArgs()->once()->andReturn(true);
        $io->shouldReceive('write')->with('/foo$/')->once()->andReturnNull();
        $io->shouldReceive('write')->with('<fg=yellow>bar</fg=yellow>')->once()->andReturnNull();

        $this->checkerPlugin->activate($this->mockComposer(), $io);
    }

    /**
     * Activate fail.
     *
     * @return void
     */
    protected function activateFail()
    {
        $process = $this->mockProcess();
        $process->shouldReceive('isSuccessful')->withNoArgs()->once()->andReturn(false);
        $process->shouldReceive('getErrorOutput')->withNoArgs()->once()->andReturn('bar');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->withNoArgs()->once()->andReturn(true);
        $io->shouldReceive('write')->with('/foo$/')->once()->andReturnNull();
        $io->shouldReceive('write')->withAnyArgs()->once()->andReturnNull();
        $io->shouldReceive('write')->with('<fg=red>bar</fg=red>')->once()->andReturnNull();

        $this->checkerPlugin->activate($this->mockComposer(), $io);
    }

    /**
     * Mock package.
     *
     * @return \Composer\Package\PackageInterface|\Mockery\MockInterface
     */
    protected function mockPackage()
    {
        $package = m::mock(PackageInterface::class);
        $package->shouldReceive('getName')->withNoArgs()->once()->andReturn(CheckerPlugin::PACKAGE_NAME)->byDefault();

        return $package;
    }

    /**
     * Mock process.
     *
     * @return \Symfony\Component\Process\Process|\Mockery\MockInterface
     */
    protected function mockProcess()
    {
        $process = m::mock(Process::class);
        $process->shouldReceive('stop')->withAnyArgs()->atMost()->once()->andReturnNull();
        $process->shouldReceive('run')->withNoArgs()->once()->andReturnNull();
        $process->shouldReceive('getCommandLine')->withNoArgs()->once()->andReturn('foo');

        $processBuilder = m::mock('overload:'.ProcessBuilder::class);
        $processBuilder->shouldReceive('getProcess')->withNoArgs()->once()->andReturn($process);

        return $process;
    }

    /**
     * Mock composer.
     *
     * @return \Composer\Composer|\Mockery\MockInterface
     */
    protected function mockComposer()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('bin-dir')->once()->andReturnNull();

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('getConfig')->withNoArgs()->once()->andReturn($config);

        return $composer;
    }

    /**
     * Mock event.
     *
     * @param mixed $operation
     *
     * @return \Composer\Installer\PackageEvent|\Mockery\MockInterface
     */
    protected function mockEvent($operation)
    {
        $event = m::mock(PackageEvent::class);
        $event->shouldReceive('getOperation')->withNoArgs()->once()->andReturn($operation);

        return $event;
    }
}
