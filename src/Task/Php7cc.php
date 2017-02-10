<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class Php7cc extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHP 7 Compatibility Checker (php7cc)';
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
            'level'          => null,
            'relative-paths' => false,
            'integer-size'   => null,
            'quiet'          => $this->io->isQuiet(),
            'verbose'        => $this->io->isVerbose(),
            'ansi'           => $this->io->isDecorated(),
            'no-ansi'        => !$this->io->isDecorated(),
            'finder'         => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('level', ['null', 'string']);
        $resolver->addAllowedValues('level', [null, 'info', 'warning', 'error']);
        $resolver->addAllowedTypes('relative-paths', ['bool']);
        $resolver->addAllowedTypes('integer-size', ['null', 'int']);
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
        $arguments = $this->processBuilder->createArgumentsForCommand('php7cc');
        $arguments->addOptionalArgument('--level=%s', $config['level']);
        $arguments->addOptionalArgument('--relative-paths', $config['relative-paths']);
        $arguments->addOptionalArgument('--integer-size', $config['integer-size']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addOptionalArgument('--ansi', $config['ansi']);
        $arguments->addOptionalArgument('--no-ansi', $config['no-ansi']);
        $arguments->addFiles($files);

        return $arguments;
    }
}
