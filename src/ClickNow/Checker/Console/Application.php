<?php

namespace ClickNow\Checker\Console;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Composer\ComposerUtil;
use ClickNow\Checker\Config\ContainerUtil;
use ClickNow\Checker\Console\Command\Git\HookCommand;
use ClickNow\Checker\Console\Helper\ComposerHelper;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Util\Git;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Application extends SymfonyConsole
{
    const APP_NAME = 'Checker';
    const APP_VERSION = '0.1.0';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \ClickNow\Checker\Console\Helper\ComposerHelper
     */
    private $composerHelper;

    /**
     * @var \ClickNow\Checker\Console\Config
     */
    private $config;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * Application.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->composerHelper = $this->initializeComposerHelper();
        $this->config = new Config($this->filesystem, $this->composerHelper->getPackage());
        $this->container = $this->initializeContainer();

        parent::__construct(self::APP_NAME, self::APP_VERSION);
    }

    /**
     * Get default input definition.
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOption($this->config->getInputOption());

        return $definition;
    }

    /**
     * Get default commands.
     *
     * @return \Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        array_push($commands, $this->container->get('console.command.run'));
        array_push($commands, $this->container->get('console.command.git.install'));
        array_push($commands, $this->container->get('console.command.git.uninstall'));

        /** @var \ClickNow\Checker\Util\Git $git */
        $git = $this->container->get('util.git');

        foreach (Git::$hooks as $hook) {
            array_push($commands, new HookCommand($this->getHookCommand($hook), $git));
        }

        return $commands;
    }

    /**
     * Get hook command.
     *
     * @param string $hook
     *
     * @throws \ClickNow\Checker\Exception\CommandInvalidException
     *
     * @return \ClickNow\Checker\Command\CommandInterface
     */
    private function getHookCommand($hook)
    {
        $command = $this->container->get(sprintf('hook.%s', $hook));

        if (!$command instanceof CommandInterface) {
            throw new CommandInvalidException($hook);
        }

        return $command;
    }

    /**
     * Get default helper set.
     *
     * @return \Symfony\Component\Console\Helper\HelperSet
     */
    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();
        $helperSet->set($this->composerHelper);

        /** @var \ClickNow\Checker\Console\Helper\PathsHelper $paths */
        $paths = $this->container->get('helper.paths');
        $helperSet->set($paths);

        /** @var \ClickNow\Checker\Console\Helper\RunnerHelper $runner */
        $runner = $this->container->get('helper.runner');
        $helperSet->set($runner);

        return $helperSet;
    }

    /**
     * Configure IO.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function configureIO(InputInterface $input, OutputInterface $output)
    {
        parent::configureIO($input, $output);

        // Register the console input and output to the container
        $this->container->set('console.input', $input);
        $this->container->set('console.output', $output);

        /** @var \ClickNow\Checker\IO\IOInterface $io */
        $io = $this->container->get('io.console');

        if ($io->isVerbose()) {
            /** @var \Monolog\Logger $logger */
            $logger = $this->container->get('logger');
            $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        }
    }

    /**
     * Initialize composer helper.
     *
     * @return \ClickNow\Checker\Console\Helper\ComposerHelper
     */
    private function initializeComposerHelper()
    {
        try {
            $config = ComposerUtil::loadConfig();
            ComposerUtil::ensureProjectBinDirInSystemPath($config->get('bin-dir'));
            $package = ComposerUtil::loadPackage($config);
        } catch (RuntimeException $e) {
            $config = null;
            $package = null;
        }

        return new ComposerHelper($config, $package);
    }

    /**
     * Initialize container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function initializeContainer()
    {
        $container = ContainerUtil::buildFromConfig($this->config);

        /** @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');
        $this->setDispatcher($eventDispatcher);

        return $container;
    }
}
