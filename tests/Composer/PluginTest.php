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
use Symfony\Component\Filesystem\Filesystem;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Composer\Plugin
     */
    protected $plugin;

    /**
     * @var string
     */
    private $tempDir;

    protected function setUp()
    {
        $this->tempDir = dirname(__DIR__.'/tmp/checker');

        $filesystem = new Filesystem();
        $filesystem->mkdir($this->tempDir);

        $this->plugin = new Plugin();
    }

    protected function tearDown()
    {
        //$filesystem = new Filesystem();
        //$filesystem->remove($this->tempDir);

        if ($this->path) {
            // Restore path if it was changed.
            putenv('PATH='.$this->path);
        }

        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(PluginInterface::class, $this->plugin);
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->plugin);
    }

    public function testActivate()
    {
        $this->plugin->activate(m::mock(Composer::class), m::mock(IOInterface::class));
    }

    public function testGetSubscribedEvent()
    {
        $this->assertInternalType('array', Plugin::getSubscribedEvents());
        $this->assertCount(5, Plugin::getSubscribedEvents());
    }


    private $path;
    private function setPath($path)
    {
        $this->path = getenv('PATH');
        putenv('PATH='.$path);
    }

    public function testPostPackageInstallEnabledWithVeryVerboseAndSuccessfully()
    {
        if (ini_get('open_basedir')) {
            echo "éee";
            $this->markTestSkipped('Cannot test when open_basedir is set');
        }


        $this->setPath(dirname(PHP_BINARY));
        $finder = basename(PHP_BINARY, '\\' === DIRECTORY_SEPARATOR ? '.exe' : '');

        if(is_executable(dirname(PHP_BINARY). '/php'))
        {
            echo "aaaaaaaaaaaaaaaaaaaaaaaaa";
        }



        //file_put_contents($this->tempDir.'/checker', '#!/usr/bin/env php');
        /*file_put_contents($this->tempDir.'/checker.bat', 'exit /b 0');
        echo 'e1='.is_executable($this->tempDir.'/checker').PHP_EOL;
        echo 'e2='.is_executable($this->tempDir.'/checker.bat').PHP_EOL;
*/
        //$this->setPath($this->tempDir);
        /*echo ;

        if(is_executable(__DIR__. '\..\..\vendor\bin\phpunit'))
        {
            echo "éee";
        }*/


        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $io->shouldReceive('write')->twice()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), InstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageInstall($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageInstallEnabledWithoutVeryVerboseAndSuccessfully()
    {
        file_put_contents($this->tempDir.'/checker.bat', 'echo 0');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(false);
        $io->shouldReceive('write')->once()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), InstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageInstall($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageInstallEnabledWithVeryVerboseAndFail()
    {
        file_put_contents($this->tempDir.'/checker.bat', 'exit /b 1');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $io->shouldReceive('write')->times(3)->andReturnNull();

        $event = $this->getEvent($this->getPackage(), InstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageInstall($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageInstallEnabledWithoutVeryVerboseAndFail()
    {
        file_put_contents($this->tempDir.'/checker.bat', 'exit /b 1');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(false);
        $io->shouldReceive('write')->twice()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), InstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageInstall($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageInstallDisabled()
    {
        $package = $this->getPackage();
        $package->shouldReceive('getName')->once()->andReturnNull();

        $event = $this->getEvent($package, InstallOperation::class);

        $this->plugin->postPackageInstall($event);
    }

    public function testPostPackageUpdateEnabledWithVeryVerboseAndSuccessfully()
    {
        file_put_contents($this->tempDir.'/checker.bat', '');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $io->shouldReceive('write')->twice()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UpdateOperation::class, 'getTargetPackage');

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageUpdate($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageUpdateEnabledWithoutVeryVerboseAndSuccessfully()
    {
        file_put_contents($this->tempDir.'/checker.bat', '');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(false);
        $io->shouldReceive('write')->once()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UpdateOperation::class, 'getTargetPackage');

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageUpdate($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageUpdateEnabledWithVeryVerboseAndFail()
    {
        file_put_contents($this->tempDir.'/checker.bat', 'exit /b 1');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $io->shouldReceive('write')->times(3)->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UpdateOperation::class, 'getTargetPackage');

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageUpdate($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageUpdateEnabledWithoutVeryVerboseAndFail()
    {
        file_put_contents($this->tempDir.'/checker.bat', 'exit /b 1');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(false);
        $io->shouldReceive('write')->twice()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UpdateOperation::class, 'getTargetPackage');

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->postPackageUpdate($event);
        $this->plugin->runScheduledTasks();
    }

    public function testPostPackageUpdateDisabled()
    {
        $package = $this->getPackage();
        $package->shouldReceive('getName')->once()->andReturnNull();

        $event = $this->getEvent($package, UpdateOperation::class, 'getTargetPackage');

        $this->plugin->postPackageUpdate($event);
    }

    public function testPrePackageUninstallEnabledWithVeryVerboseAndSuccessfully()
    {
        file_put_contents($this->tempDir.'/checker.bat', '');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $io->shouldReceive('write')->twice()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UninstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->prePackageUninstall($event);
    }

    public function testPrePackageUninstallEnabledWithoutVeryVerboseAndSuccessfully()
    {
        file_put_contents($this->tempDir.'/checker.bat', '');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(false);
        $io->shouldReceive('write')->once()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UninstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->prePackageUninstall($event);
    }

    public function testPrePackageUninstallEnabledWithVeryVerboseAndFail()
    {
        file_put_contents($this->tempDir.'/checker.bat', 'exit /b 1');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(true);
        $io->shouldReceive('write')->times(3)->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UninstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->prePackageUninstall($event);
    }

    public function testPrePackageUninstallEnabledWithoutVeryVerboseAndFail()
    {
        file_put_contents($this->tempDir.'/checker.bat', 'exit /b 1');

        $io = m::mock(IOInterface::class);
        $io->shouldReceive('isVeryVerbose')->once()->andReturn(false);
        $io->shouldReceive('write')->twice()->andReturnNull();

        $event = $this->getEvent($this->getPackage(), UninstallOperation::class);

        $this->plugin->activate($this->getComposer(), $io);
        $this->plugin->prePackageUninstall($event);
    }

    public function testPrePackageUninstallDisabled()
    {
        $package = $this->getPackage();
        $package->shouldReceive('getName')->once()->andReturnNull();

        $event = $this->getEvent($package, UninstallOperation::class);

        $this->plugin->prePackageUninstall($event);
    }

    protected function getPackage()
    {
        $package = m::mock(PackageInterface::class);
        $package->shouldReceive('getName')->once()->andReturn(Plugin::PACKAGE_NAME)->byDefault();

        return $package;
    }

    protected function getEvent($package, $operation, $method = 'getPackage')
    {
        $operation = m::mock($operation);
        $operation->shouldReceive($method)->once()->andReturn($package);

        $event = m::mock(PackageEvent::class);
        $event->shouldReceive('getOperation')->once()->andReturn($operation);

        return $event;
    }

    protected function getComposer()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('bin-dir')->andReturn($this->tempDir);

        $composer = m::mock(Composer::class);
        $composer->shouldReceive('getConfig')->once()->andReturn($config);

        return $composer;
    }
}
