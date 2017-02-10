<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class Behat extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Behat';
    }

    /**
     * Get config options.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function getConfigOptions(RunnerInterface $runner)
    {
        $resolver = parent::getConfigOptions($runner);

        $resolver->setDefaults([
            'suite'           => null,
            'format'          => [],
            'out'             => [],
            'format-settings' => [],
            'lang'            => null,
            'name'            => [],
            'tags'            => [],
            'role'            => null,
            'strict'          => false,
            'order'           => null,
            'rerun'           => false,
            'stop-on-failure' => $runner->isStopOnFailure(),
            'profile'         => null,
            'config'          => null,
            'verbose'         => $this->io->isVerbose(),
            'colors'          => false,
            'no-colors'       => false,
            'finder'          => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('suite', ['null', 'string']);
        $resolver->addAllowedTypes('format', ['array']);
        $resolver->addAllowedTypes('out', ['array']);
        $resolver->addAllowedTypes('format-settings', ['array']);
        $resolver->addAllowedTypes('lang', ['null', 'string']);
        $resolver->addAllowedTypes('name', ['array']);
        $resolver->addAllowedTypes('tags', ['array']);
        $resolver->addAllowedTypes('role', ['null', 'string']);
        $resolver->addAllowedTypes('strict', ['bool']);
        $resolver->addAllowedTypes('order', ['null', 'string']);
        $resolver->addAllowedTypes('rerun', ['bool']);
        $resolver->addAllowedTypes('stop-on-failure', ['bool']);
        $resolver->addAllowedTypes('profile', ['null', 'string']);
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('verbose', ['bool']);
        $resolver->addAllowedTypes('colors', ['bool']);
        $resolver->addAllowedTypes('no-colors', ['bool']);

        return $resolver;
    }

    /**
     * Create arguments.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     *
     * @return \ClickNow\Checker\Process\ArgumentsCollection
     */
    protected function createArguments(array $config, FilesCollection $files)
    {
        $arguments = $this->processBuilder->createArgumentsForCommand('behat');
        $arguments->addOptionalArgument('--suite=%s', $config['suite']);
        $arguments->addArgumentArray('--format=%s', $config['format']);
        $arguments->addArgumentArray('--out=%s', $config['out']);
        $arguments->addArgumentArray('--format-settings=%s', $config['format-settings']);
        $arguments->addOptionalArgument('--lang=%s', $config['lang']);
        $arguments->addArgumentArray('--name=%s', $config['name']);
        $arguments->addArgumentArray('--tags=%s', $config['tags']);
        $arguments->addOptionalArgument('--role=%s', $config['role']);
        $arguments->addOptionalArgument('--strict', $config['strict']);
        $arguments->addOptionalArgument('--order=%s', $config['order']);
        $arguments->addOptionalArgument('--rerun', $config['rerun']);
        $arguments->addOptionalArgument('--stop-on-failure', $config['stop-on-failure']);
        $arguments->addOptionalArgument('--profile=%s', $config['profile']);
        $arguments->addOptionalArgument('--config=%s', $config['config']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addOptionalArgument('--colors', $config['colors']);
        $arguments->addOptionalArgument('--no-colors', $config['no-colors']);

        return $arguments;
    }
}
