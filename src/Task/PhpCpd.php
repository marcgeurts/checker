<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class PhpCpd extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHP Copy/Paste Detector (phpcpd)';
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
            'paths'      => '.',
            'log-pmd'    => null,
            'min-lines'  => null,
            'min-tokens' => null,
            'fuzzy'      => false,
            'quiet'      => $this->io->isQuiet(),
            'verbose'    => $this->io->isVerbose(),
            'ansi'       => $this->io->isDecorated(),
            'no-ansi'    => !$this->io->isDecorated(),
            'finder'     => [
                'name'     => ['*.php'],
                'not-path' => ['vendor'],
            ],
        ]);

        $resolver->addAllowedTypes('paths', ['string', 'array']);
        $resolver->addAllowedTypes('log-pmd', ['null', 'string']);
        $resolver->addAllowedTypes('min-lines', ['null', 'int']);
        $resolver->addAllowedTypes('min-tokens', ['null', 'int']);
        $resolver->addAllowedTypes('fuzzy', ['bool']);
        $resolver->addAllowedTypes('quiet', ['bool']);
        $resolver->addAllowedTypes('verbose', ['bool']);
        $resolver->addAllowedTypes('ansi', ['bool']);
        $resolver->addAllowedTypes('no-ansi', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('phpcpd');
        $arguments->addArgumentArray('%s', (array) $config['paths']);
        $arguments->addOptionalCommaSeparatedArgument('--names=%s', $config['finder']['name']);
        $arguments->addArgumentArray('--names-exclude=%s', $config['finder']['not-name']);
        $arguments->addArgumentArray('--exclude=%s', $config['finder']['not-path']);
        $arguments->addOptionalArgument('--log-pmd=%s', $config['log-pmd']);
        $arguments->addOptionalArgument('--min-lines=%u', $config['min-lines']);
        $arguments->addOptionalArgument('--min-tokens=%u', $config['min-tokens']);
        $arguments->addOptionalArgument('--fuzzy', $config['fuzzy']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addOptionalArgument('--ansi', $config['ansi']);
        $arguments->addOptionalArgument('--no-ansi', $config['no-ansi']);

        return $arguments;
    }
}
