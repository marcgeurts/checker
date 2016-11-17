<?php

namespace ClickNow\Checker\Console;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Config\ContainerFactory;
use ClickNow\Checker\Console\Command\Git\HookCommand;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Repository\Git;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\Loader\JsonLoader;
use Composer\Package\Loader\RootPackageLoader;
use Composer\Repository\RepositoryFactory;
use Exception;
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
        $this->config = new Config(new Filesystem(), $this->initializeComposerPackage());
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
        array_push($commands, $this->container->get('command.run'));
        array_push($commands, $this->container->get('command.git.install'));
        array_push($commands, $this->container->get('command.git.uninstall'));

        /** @var \ClickNow\Checker\Repository\Git $git */
        $git = $this->container->get('repository.git');

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
     * Initialize container.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function initializeContainer()
    {
        $container = ContainerFactory::create($this->config->getPath());
        $container->set('console.config', $this->config);

        /** @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $eventDispatcher */
        $eventDispatcher = $container->get('event_dispatcher');
        $this->setDispatcher($eventDispatcher);

        return $container;
    }

    /**
     * Initialize composer package.
     *
     * @return \Composer\Package\PackageInterface|null
     */
    private function initializeComposerPackage()
    {
        $package = null;

        try {
            $config = Factory::createConfig();
            $this->ensureProjectBinDirInSystemPath($config->get('bin-dir'));
            $loader = new JsonLoader(new RootPackageLoader(RepositoryFactory::manager(new NullIO(), $config), $config));
            $package = $loader->load(getcwd().DIRECTORY_SEPARATOR.'composer.json');
        } catch (Exception $e) {
        }

        return $package;
    }

    /**
     * Composer contains some logic to prepend the current bin dir to the system PATH.
     * To make sure this application works the same in CLI and Composer modus,
     * we'll have to ensure that the bin path is always prefixed.
     *
     * @param string $binDir
     *
     * @return void
     */
    private function ensureProjectBinDirInSystemPath($binDir)
    {
        $absoluteBinDir = realpath($binDir);
        $pathStr = (!isset($_SERVER['PATH']) && isset($_SERVER['Path'])) ? 'Path' : 'PATH';
        $match = preg_match(
            '{(^|'.PATH_SEPARATOR.')'.preg_quote($absoluteBinDir).'($|'.PATH_SEPARATOR.')}',
            $_SERVER[$pathStr]
        );

        if (!is_dir($absoluteBinDir) || !isset($_SERVER[$pathStr]) || $match) {
            return;
        }

        $_SERVER[$pathStr] = $absoluteBinDir.PATH_SEPARATOR.getenv($pathStr);
        putenv($pathStr.'='.$_SERVER[$pathStr]);
    }
}
