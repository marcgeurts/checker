<?php

namespace ClickNow\Checker\Config;

use ClickNow\Checker\Config\Compiler\CommandCompilerPass;
use ClickNow\Checker\Config\Compiler\ExtensionCompilerPass;
use ClickNow\Checker\Config\Compiler\GitHookCompilerPass;
use ClickNow\Checker\Config\Compiler\TaskCompilerPass;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Filesystem\Filesystem;

final class ContainerFactory
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private $containerBuilder;

    /**
     * Container factory.
     *
     * @param string $configPath
     */
    private function __construct($configPath)
    {
        $this->filesystem = new Filesystem();
        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder->setProxyInstantiator(new RuntimeInstantiator());
        $this->registerCompilerPass();
        $this->loadServices($configPath);
    }

    /**
     * Create.
     *
     * @param string $configPath
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function create($configPath)
    {
        $container = new self($configPath);

        // Compile config to make sure that
        // 1. Extensions were registered
        // 2. Tasks merged default config
        // 3. Commands with their tasks and commands are added to the CommandsCollection
        // 4. Git hooks are configured with their tasks and commands
        $container->containerBuilder->compile();

        return $container->containerBuilder;
    }

    /**
     * Register compiler pass.
     *
     * @return void
     */
    private function registerCompilerPass()
    {
        $this->containerBuilder->addCompilerPass(new ExtensionCompilerPass());
        $this->containerBuilder->addCompilerPass(new TaskCompilerPass());
        $this->containerBuilder->addCompilerPass(new CommandCompilerPass());
        $this->containerBuilder->addCompilerPass(new GitHookCompilerPass());
        $this->containerBuilder->addCompilerPass(
            new RegisterListenersPass('event-dispatcher', 'checker.event-listener', 'checker.event-subscriber')
        );
    }

    /**
     * Load services.
     *
     * @param string $configPath
     *
     * @return void
     */
    private function loadServices($configPath)
    {
        $loader = new YamlFileLoader($this->containerBuilder, new FileLocator(__DIR__.'/../../../../resources/config'));
        $loader->load('config.yml');

        if ($this->filesystem->exists($configPath)) {
            $loader->load($configPath);
        }
    }
}
