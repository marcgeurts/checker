<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class Gherkin extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Gherkin';
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
            'directory' => 'features',
            'align'     => null,
            'quiet'     => $this->io->isQuiet(),
            'verbose'   => $this->io->isVerbose(),
            'ansi'      => $this->io->isDecorated(),
            'no-ansi'   => !$this->io->isDecorated(),
            'finder' => ['extensions' => ['feature']],
        ]);

        $resolver->addAllowedTypes('directory', ['string']);
        $resolver->addAllowedTypes('align', ['null', 'string']);
        $resolver->addAllowedValues('align', [null, 'left', 'right']);
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
        $arguments = $this->processBuilder->createArgumentsForCommand('kawaii');
        $arguments->add('gherkin:check');
        $arguments->add($config['directory']);
        $arguments->addOptionalArgument('--align=%s', $config['align']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addOptionalArgument('--ansi', $config['ansi']);
        $arguments->addOptionalArgument('--no-ansi', $config['no-ansi']);

        return $arguments;
    }
}
