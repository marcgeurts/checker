<?php

namespace ClickNow\Checker\Console;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Config\ContainerFactory;
use ClickNow\Checker\Console\Command\Git\HookCommand;
use ClickNow\Checker\Console\Helper\ComposerHelper;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Util\ComposerUtil;
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
        $definition->addOption($this->getConfig()->getInputOption());

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
        array_push($commands, $this->getContainer()->get('command.run'));
        array_push($commands, $this->getContainer()->get('command.git.install'));
        array_push($commands, $this->getContainer()->get('command.git.uninstall'));

        /** @var \ClickNow\Checker\Util\Git $git */
        $git = $this->getContainer()->get('util.git');

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
        $command = $this->getContainer()->get(sprintf('hook.%s', $hook));

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
        $paths = $this->getContainer()->get('helper.paths');
        $helperSet->set($paths);

        /** @var \ClickNow\Checker\Console\Helper\RunnerHelper $runner */
        $runner = $this->getContainer()->get('helper.runner');
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
        $this->getContainer()->set('console.input', $input);
        $this->getContainer()->set('console.output', $output);

        /** @var \ClickNow\Checker\IO\IOInterface $io */
        $io = $this->getContainer()->get('io.console');
        if ($io->isVerbose()) {
            /** @var \Monolog\Logger $logger */
            $logger = $this->getContainer()->get('logger');
            $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        }
    }

    /**
     * Get composer helper.
     *
     * @return \ClickNow\Checker\Console\Helper\ComposerHelper
     */
    private function getComposerHelper()
    {
        if ($this->composerHelper) {
            return $this->composerHelper;
        }

        try {
            $config = ComposerUtil::loadConfig();
            ComposerUtil::ensureProjectBinDirInSystemPath($config->get('bin-dir'));
            $package = ComposerUtil::loadPackage($config);
        } catch (RuntimeException $e) {
            $config = null;
            $package = null;
        }

        $this->composerHelper = new ComposerHelper($config, $package);

        return $this->composerHelper;
    }

    /**
     * Get config.
     *
     * @return \ClickNow\Checker\Console\Config
     */
    private function getConfig()
    {
        if ($this->config) {
            return $this->config;
        }

        $this->config = new Config(new Filesystem(), $this->getComposerHelper()->getPackage());

        return $this->config;
    }

    /**
     * Get container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function getContainer()
    {
        if ($this->container) {
            return $this->container;
        }

        $this->container = ContainerFactory::create($this->getConfig()->getPath());
        $this->container->set('console.config', $this->getConfig());

        /** @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $this->setDispatcher($eventDispatcher);

        return $this->container;
    }
}
