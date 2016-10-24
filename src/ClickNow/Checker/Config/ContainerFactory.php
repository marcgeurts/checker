<?php

namespace ClickNow\Checker\Config;

use ClickNow\Checker\Config\Compiler\CommandCompilerPass;
use ClickNow\Checker\Config\Compiler\ExtensionCompilerPass;
use ClickNow\Checker\Config\Compiler\HookCompilerPass;
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
     * Create container builder from config path.
     *
     * @param string $configPath
     * @param string $defaultConfigPath
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function buildFromConfigPath($configPath, $defaultConfigPath)
    {
        $container = new ContainerBuilder();
        $container->setProxyInstantiator(new RuntimeInstantiator());

        // Add compiler passes
        $container->addCompilerPass(new ExtensionCompilerPass());
        $container->addCompilerPass(new TaskCompilerPass());
        $container->addCompilerPass(new CommandCompilerPass());
        $container->addCompilerPass(new HookCompilerPass());
        $container->addCompilerPass(
            new RegisterListenersPass('event_dispatcher', 'checker.event_listener', 'checker.event_subscriber')
        );

        // Load basic service file + custom user config
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../../resources/config'));
        $loader->load('config.yml');

        // Load checker.yml
        $filesystem = new Filesystem();
        if ($filesystem->exists($configPath)) {
            $loader->load($configPath);
        }

        // Set parameter default config path
        $container->setParameter('default_config_path', $defaultConfigPath);

        // Compile config to make sure that
        // 1. Extensions were registered
        // 2. Tasks merged default config
        // 3. Commands with their tasks and commands are added to the CommandsCollection
        // 4. Hooks are configured with their tasks and commands
        $container->compile();

        return $container;
    }
}
