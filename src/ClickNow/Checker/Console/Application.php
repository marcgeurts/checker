<?php

namespace ClickNow\Checker\Console;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Config\ContainerFactory;
use ClickNow\Checker\Console\Command\Git\HookCommand;
use ClickNow\Checker\Console\Helper\ComposerHelper;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Util\Composer;
use ClickNow\Checker\Util\Git;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Application extends SymfonyConsole
{
    const APP_NAME = 'Checker';
    const APP_VERSION = '0.1.0';
    const APP_CONFIG_FILE = 'checker.yml';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \ClickNow\Checker\Console\Helper\ComposerHelper
     */
    private $composerHelper;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $container;

    /**
     * @var string
     */
    private $defaultConfigPath;

    /**
     * Application.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();

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
            $this->getDefaultConfigPath()
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
        $container = $this->getContainer();

        $commands = parent::getDefaultCommands();
        $commands[] = $container->get('command.run');
        $commands[] = $container->get('command.git.install');
        $commands[] = $container->get('command.git.uninstall');

        foreach (Git::$hooks as $hook) {
            $commands[] = new HookCommand($this->getHookCommand($hook), $container->get('util.git'));
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

        if (! $command instanceof CommandInterface) {
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
        $container = $this->getContainer();

        $helperSet = parent::getDefaultHelperSet();
        $helperSet->set($this->getComposerHelper());
        $helperSet->set($container->get('helper.paths'));
        $helperSet->set($container->get('helper.runner'));

        return $helperSet;
    }

    /**
     * Configure IO.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function configureIO(InputInterface $input, OutputInterface $output)
    {
        parent::configureIO($input, $output);

        $container = $this->getContainer();

        // Register the console input and output to the container
        $container->set('console.input', $input);
        $container->set('console.output', $output);

        /** @var \ClickNow\Checker\IO\IOInterface $io */
        $io = $container->get('io.console');
        if ($io->isVerbose()) {
            /** @var \Monolog\Logger $logger */
            $logger = $container->get('logger');
            $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        }
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

        $defaultConfigPath = $this->getDefaultConfigPath();

        // Load cli options
        $input = new ArgvInput();

        $configPath = $input->getParameterOption(['--config', '-c'], $defaultConfigPath);

        // Build the service container
        $this->container = ContainerFactory::buildFromConfigPath($configPath, $defaultConfigPath);
        $this->setDispatcher($this->container->get('event_dispatcher'));

        return $this->container;
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
            $config = Composer::loadConfig();
            Composer::ensureProjectBinDirInSystemPath($config->get('bin-dir'));
            $rootPackage = Composer::loadRootPackage($config);
        } catch (RuntimeException $e) {
            $config = null;
            $rootPackage = null;
        }

        $this->composerHelper = new ComposerHelper($config, $rootPackage);

        return $this->composerHelper;
    }

    /**
     * Get default config path.
     *
     * @return string
     */
    private function getDefaultConfigPath()
    {
        if ($this->defaultConfigPath) {
            return $this->defaultConfigPath;
        }

        $defaultPath = getcwd().DIRECTORY_SEPARATOR.self::APP_CONFIG_FILE;

        // use path from composer
        $package = $this->getComposerHelper()->getRootPackage();
        if (! is_null($package)) {
            $extra = $package->getExtra();

            if (isset($extra['checker']['config'])) {
                $defaultPath = $extra['checker']['config'];
            }
        }

        // use path with dist
        $distPath = (strpos($defaultPath, -5) !== '.dist') ? $defaultPath.'.dist' : $defaultPath;
        if ($this->filesystem->exists($distPath)) {
            $defaultPath = $distPath;
        }

        // Make sure to set the full path when it is declared relative
        // This will fix some issues in windows.
        if (! $this->filesystem->isAbsolutePath($defaultPath)) {
            $defaultPath = getcwd().DIRECTORY_SEPARATOR.$defaultPath;
        }

        $this->defaultConfigPath = $defaultPath;

        return $this->defaultConfigPath;
    }
}
