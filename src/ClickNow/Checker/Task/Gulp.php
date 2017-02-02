<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Repository\FilesCollection;

class Gulp extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'gulp';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // gulpfile
        $resolver->setDefault('gulpfile', null);
        $resolver->addAllowedTypes('gulpfile', ['null', 'string']);

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
        $arguments->addOptionalArgument('--gulpfile=%s', $config['gulpfile']);
        $arguments->addOptionalArgument('%s', $config['task']);
    }
}
