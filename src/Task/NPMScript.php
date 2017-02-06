<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class NpmScript extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'npm-script';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // script
        $resolver->setDefault('script', null);
        $resolver->addAllowedTypes('script', ['string']);

        // working-directory
        $resolver->setDefault('working-directory', './');
        $resolver->addAllowedTypes('working-directory', ['string']);

        // is-run-task
        $resolver->setDefault('is-run-task', false);
        $resolver->addAllowedTypes('is-run-task', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('npm');
        $arguments->addOptionalArgument('run', $config['is-run-task']);
        $arguments->addRequiredArgument('%s', $config['script']);

        return $arguments;
    }

    /**
     * Build process.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param \ClickNow\Checker\Runner\RunnerInterface     $runner
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function buildProcess(array $config, FilesCollection $files, RunnerInterface $runner)
    {
        $process = parent::buildProcess($config, $files, $runner);
        $process->setWorkingDirectory($config['working-directory']);

        return $process;
    }
}
