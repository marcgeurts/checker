<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class Brunch extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Brunch';
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
            'task'  => 'build',
            'env'   => null,
            'jobs'  => null,
            'debug' => false,
        ]);

        $resolver->addAllowedTypes('task', ['string']);
        $resolver->addAllowedTypes('env', ['null', 'string']);
        $resolver->addAllowedTypes('jobs', ['null', 'int']);
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
