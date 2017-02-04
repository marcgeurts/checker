<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class Atoum extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'atoum';
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

        // bootstrap-file
        $resolver->setDefault('bootstrap-file', null);
        $resolver->addAllowedTypes('bootstrap-file', ['null', 'string']);

        // directories
        $resolver->setDefault('directories', []);
        $resolver->addAllowedTypes('directories', ['array']);

        // files
        $resolver->setDefault('files', []);
        $resolver->addAllowedTypes('files', ['array']);

        // namespaces
        $resolver->setDefault('namespaces', []);
        $resolver->addAllowedTypes('namespaces', ['array']);

        // methods
        $resolver->setDefault('methods', []);
        $resolver->addAllowedTypes('methods', ['array']);

        // tags
        $resolver->setDefault('tags', []);
        $resolver->addAllowedTypes('tags', ['array']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('atoum');
        $arguments->addOptionalArgumentWithSeparatedValue('--configuration', $config['configuration']);
        $arguments->addOptionalArgumentWithSeparatedValue('--bootstrap-file', $config['bootstrap-file']);
        $arguments->addSeparatedArgumentArray('--directories', $config['directories']);
        $arguments->addSeparatedArgumentArray('--files', $config['files']);
        $arguments->addSeparatedArgumentArray('--namespaces', $config['namespaces']);
        $arguments->addSeparatedArgumentArray('--methods', $config['methods']);
        $arguments->addSeparatedArgumentArray('--tags', $config['tags']);

        return $arguments;
    }
}
