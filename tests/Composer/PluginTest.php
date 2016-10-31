<?php

use ClickNow\Checker\Composer\Plugin;
use Mockery as m;

class PluginTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Composer\Plugin
     */
    protected $plugin;

    protected function setUp()
    {
        $this->plugin = new Plugin();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testActivate()
    {
        $this->plugin->activate(m::mock('Composer\Composer'), m::mock('Composer\IO\IOInterface'));
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', Plugin::getSubscribedEvents());
    }

    public function testPostPackageInstall()
    {
        $operation = m::mock('Composer\DependencyResolver\Operation\InstallOperation');
        $operation->shouldReceive('getPackage')->twice()->andReturn($this->getPackage());

        $event = $this->prepareToRun($operation);

        // enabled
        $this->plugin->postPackageInstall($event);
        $this->plugin->runScheduledTasks();

        // disabled
        $this->plugin->postPackageInstall($event);
    }

    public function testPostPackageUpdate()
    {
        $operation = m::mock('Composer\DependencyResolver\Operation\UpdateOperation');
        $operation->shouldReceive('getTargetPackage')->twice()->andReturn($this->getPackage());

        $event = $this->prepareToRun($operation);

        // enabled
        $this->plugin->postPackageUpdate($event);
        $this->plugin->runScheduledTasks();

        // disabled
        $this->plugin->postPackageUpdate($event);
    }

    public function testPrePackageUninstall()
    {
        $operation = m::mock('Composer\DependencyResolver\Operation\UninstallOperation');
        $operation->shouldReceive('getPackage')->twice()->andReturn($this->getPackage());

        $event = $this->prepareToRun($operation);

        // enabled
        $this->plugin->prePackageUninstall($event);
        $this->plugin->runScheduledTasks();

        // disabled
        $this->plugin->prePackageUninstall($event);
    }

    protected function getPackage()
    {
        $package =  m::mock('Composer\Package\PackageInterface');
        $package->shouldReceive('getName')->twice()->andReturnValues([Plugin::PACKAGE_NAME, null]);

        return $package;
    }

    protected function prepareToRun($operation)
    {
        $event = m::mock('Composer\Installer\PackageEvent');
        $event->shouldReceive('getOperation')->twice()->andReturn($operation);

        $config = m::mock('Composer\Config');
        $config->shouldReceive('get')->once()->with('bin-dir')->andReturnNull();

        $composer = m::mock('Composer\Composer');
        $composer->shouldReceive('getConfig')->once()->andReturn($config);

        $io = m::mock('Composer\IO\IOInterface');
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $io->shouldReceive('write')->times(3)->andReturnNull();

        $this->plugin->activate($composer, $io);

        return $event;
    }
}
