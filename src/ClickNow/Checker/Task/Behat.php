<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Repository\FilesCollection;

class Behat extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'behat';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // config
        $resolver->setDefault('config', null);
        $resolver->addAllowedTypes('config', ['null', 'string']);

        // format
        $resolver->setDefault('format', []);
        $resolver->addAllowedTypes('format', ['array']);

        // suite
        $resolver->setDefault('suite', null);
        $resolver->addAllowedTypes('suite', ['null', 'string']);

        return $resolver;
    }

    /**
     * Add arguments.
     *
     * @param \ClickNow\Checker\Process\ArgumentsCollection $arguments
     * @param array                                         $config
     * @param \ClickNow\Checker\Repository\FilesCollection  $files
     */
    protected function addArguments(ArgumentsCollection $arguments, array $config, FilesCollection $files)
    {
        $arguments->addOptionalArgumentWithSeparatedValue('--config', $config['config']);
        $arguments->addArgumentArray('--format=%s', $config['format']);
        $arguments->addOptionalArgument('--suite=%s', $config['suite']);
    }
}
