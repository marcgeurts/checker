<?php

namespace ClickNow\Checker\Task;

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
     * Create arguments.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     *
     * @return \ClickNow\Checker\Process\ArgumentsCollection
     */
    protected function createArguments(array $config, FilesCollection $files)
    {
        $arguments = $this->processBuilder->createArgumentsForCommand('gulp');
        $arguments->addOptionalArgument('--gulpfile=%s', $config['gulpfile']);
        $arguments->addOptionalArgument('%s', $config['task']);

        return $arguments;
    }
}
