<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Repository\FilesCollection;

class PHPUnit extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'phpunit';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // configuration
        $resolver->setDefault('configuration', null);
        $resolver->addAllowedTypes('configuration', ['null', 'string']);

        // group
        $resolver->setDefault('group', []);
        $resolver->addAllowedTypes('group', ['array']);

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
        $arguments->addOptionalArgument('--configuration=%s', $config['configuration']);
        $arguments->addOptionalCommaSeparatedArgument('--group=%s', $config['group']);
    }
}