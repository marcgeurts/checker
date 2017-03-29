<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class SecurityChecker extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Security Checker';
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
            'lockfile'  => './composer.lock',
            'format'    => null,
            'end-point' => null,
            'timeout'   => null
        ]);

        $resolver->addAllowedTypes('lockfile', ['null', 'string']);
        $resolver->addAllowedTypes('format', ['null', 'string']);
        $resolver->addAllowedTypes('end-point', ['null', 'string']);
        $resolver->addAllowedTypes('timeout', ['null', 'string']);
        $resolver->addAllowedValues('format', [null, 'text', 'json', 'simple']);

        return $resolver;
    }

    /**
     * Finder files.
     *
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param array                                        $config
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    protected function finderFiles(FilesCollection $files, array $config)
    {
         return $config['lockfile'] ? $files->filterByName($config['lockfile']) : new FilesCollection;
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
        $arguments = $this->processBuilder->createArgumentsForCommand('security-checker');
        $arguments->add('security:check');
        $arguments->addOptionalArgument('--format=%s', $config['format']);
        $arguments->addOptionalArgument('--end-point=%s', $config['end-point']);
        $arguments->addOptionalArgument('--timeout=%s', $config['timeout']);
        $arguments->addOptionalArgument('%s', $config['lockfile']);

        return $arguments;
    }
}
