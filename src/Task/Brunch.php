<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class Brunch extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'brunch';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        $resolver->setDefaults([
            'task'  => 'build',
            'env'   => 'production',
            'jobs'  => 4,
            'debug' => false,
        ]);

        $resolver->addAllowedTypes('task', ['string']);
        $resolver->addAllowedTypes('env', ['string']);
        $resolver->addAllowedTypes('jobs', ['int']);
        $resolver->addAllowedTypes('debug', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('brunch');
        $arguments->addRequiredArgument('%s', $config['task']);
        $arguments->addOptionalArgumentWithSeparatedValue('--env', $config['env']);
        $arguments->addOptionalArgumentWithSeparatedValue('--jobs', $config['jobs']);
        $arguments->addOptionalArgument('--debug', $config['debug']);

        return $arguments;
    }
}
