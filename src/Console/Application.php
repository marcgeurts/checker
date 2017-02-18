<?php

namespace ClickNow\Checker\Console;

use ClickNow\Checker\Composer\ComposerUtil;
use ClickNow\Checker\Config\ContainerFactory;
use ClickNow\Checker\Repository\Filesystem;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends SymfonyConsole
{
    const APP_NAME = 'Checker';
    const APP_VERSION = '1.0.0-3';

    /**
     * @var \ClickNow\Checker\Console\ConfigFile
     */
    private $configFile;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * Application.
     */
    public function __construct()
    {
        $this->configFile = new ConfigFile(new Filesystem(), ComposerUtil::loadPackage());
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
        $definition->addOption(new InputOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Path to config',
            $this->configFile->getDefaultPath()
        ));

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

        $gitHooks = array_keys((array) $this->container->getParameter('git-hooks'));
        foreach ($gitHooks as $gitHook) {
            array_push($commands, $this->container->get(sprintf('console.command.git.%s', $gitHook)));
        }

        return $commands;
    }

    /**
     * Get default helper set.
     *
     * @return \Symfony\Component\Console\Helper\HelperSet
     */
    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        /** @var \ClickNow\Checker\Helper\PathsHelper $paths */
        $paths = $this->container->get('helper.paths');
        $helperSet->set($paths);

        /** @var \ClickNow\Checker\Helper\RunnerHelper $runner */
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
     * Initialize container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function initializeContainer()
    {
        $input = new ArgvInput();
        $configPath = $input->getParameterOption(['--config', '-c']) ?: $this->configFile->getDefaultPath();

        $container = ContainerFactory::create($configPath);
        $container->set('console.config-file', $this->configFile);

        /** @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->get('event-dispatcher');
        $this->setDispatcher($eventDispatcher);

        return $container;
    }
}
