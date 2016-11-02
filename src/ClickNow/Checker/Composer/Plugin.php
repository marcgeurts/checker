<?php

namespace ClickNow\Checker\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    const PACKAGE_NAME = 'cknow/checker';

    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * @var bool
     */
    protected $gitInstallScheduled = false;

    /**
     * Apply plugin modifications to Composer.
     *
     * @param \Composer\Composer       $composer
     * @param \Composer\IO\IOInterface $io
     *
     * @return void
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Get subscribed events.
     *
     * @return array<*,string>
     */
    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_INSTALL  => 'postPackageInstall',
            PackageEvents::POST_PACKAGE_UPDATE   => 'postPackageUpdate',
            PackageEvents::PRE_PACKAGE_UNINSTALL => 'prePackageUninstall',
            ScriptEvents::POST_INSTALL_CMD       => 'runScheduledTasks',
            ScriptEvents::POST_UPDATE_CMD        => 'runScheduledTasks',
        ];
    }

    /**
     * Post package install.
     *
     * @param \Composer\Installer\PackageEvent $event
     *
     * @return void
     */
    public function postPackageInstall(PackageEvent $event)
    {
        /** @var \Composer\DependencyResolver\Operation\InstallOperation $operation */
        $operation = $event->getOperation();
        $package = $operation->getPackage();

        if (!$this->isThisPackage($package)) {
            return;
        }

        $this->gitInstallScheduled = true;
    }

    /**
     * Post package update.
     *
     * @param \Composer\Installer\PackageEvent $event
     *
     * @return void
     */
    public function postPackageUpdate(PackageEvent $event)
    {
        /** @var \Composer\DependencyResolver\Operation\UpdateOperation $operation */
        $operation = $event->getOperation();
        $package = $operation->getTargetPackage();

        if (!$this->isThisPackage($package)) {
            return;
        }

        $this->gitInstallScheduled = true;
    }

    /**
     * Pre package uninstall.
     *
     * @param \Composer\Installer\PackageEvent $event
     *
     * @return void
     */
    public function prePackageUninstall(PackageEvent $event)
    {
        /** @var \Composer\DependencyResolver\Operation\UninstallOperation $operation */
        $operation = $event->getOperation();
        $package = $operation->getPackage();

        if (!$this->isThisPackage($package)) {
            return;
        }

        $this->runCommand('git:uninstall');
    }

    /**
     * Run scheduled tasks.
     *
     * @return void
     */
    public function runScheduledTasks()
    {
        if ($this->gitInstallScheduled) {
            $this->runCommand('git:install');
        }
    }

    /**
     * Is this package?
     *
     * @param \Composer\Package\PackageInterface $package
     *
     * @return bool
     */
    private function isThisPackage(PackageInterface $package)
    {
        return $package->getName() == self::PACKAGE_NAME;
    }

    /**
     * Run command.
     *
     * @param string $command
     *
     * @return void
     */
    private function runCommand($command)
    {
        $config = $this->composer->getConfig();
        $finder = new ExecutableFinder();
        $executable = $finder->find('checker', null, [$config->get('bin-dir')]);
        $builder = new ProcessBuilder([$executable, $command, '--no-interaction']);
        echo $builder->getProcess();
        $this->runProcess($builder->getProcess());
    }

    /**
     * Run process.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return void
     */
    private function runProcess(Process $process)
    {
        if ($this->io->isVeryVerbose()) {
            $this->io->write('Running process: '.$process->getCommandLine());
        }

        $process->run();

        if (!$process->isSuccessful()) {
            $this->io->write(sprintf(
                '<fg=red>%s</fg=red>',
                'Checker has not been installed in git hooks! Did you specify the correct git-dir?'
            ));
            $this->io->write(sprintf('<fg=red>%s</fg=red>', $process->getErrorOutput()));

            return;
        }

        $this->io->write(sprintf('<fg=yellow>%s</fg=yellow>', $process->getOutput()));
    }
}
