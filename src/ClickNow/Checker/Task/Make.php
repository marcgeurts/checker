<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Repository\FilesCollection;

class Make extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'make';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // makefile
        $resolver->setDefault('makefile', null);
        $resolver->addAllowedTypes('makefile', ['null', 'string']);

        // task
        $resolver->setDefault('task', null);
        $resolver->addAllowedTypes('task', ['null', 'string']);

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
        $arguments->addOptionalArgument('--makefile=%s', $config['makefile']);
        $arguments->addOptionalArgument('%s', $config['task']);
    }
}
