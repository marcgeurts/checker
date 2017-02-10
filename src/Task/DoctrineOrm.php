<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class DoctrineOrm extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Doctrine ORM';
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
            'skip-mapping' => false,
            'skip-sync'    => false,
            'quiet'        => $this->io->isQuiet(),
            'verbose'      => $this->io->isVerbose(),
            'ansi'         => $this->io->isDecorated(),
            'no-ansi'      => !$this->io->isDecorated(),
            'finder'       => ['extensions' => ['php', 'xml', 'yml']],
        ]);

        $resolver->addAllowedTypes('skip-mapping', ['bool']);
        $resolver->addAllowedTypes('skip-sync', ['bool']);
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
        $arguments = $this->processBuilder->createArgumentsForCommand('doctrine');
        $arguments->add('orm:validate-schema');
        $arguments->addOptionalArgument('--skip-mapping', $config['skip-mapping']);
        $arguments->addOptionalArgument('--skip-sync', $config['skip-sync']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addOptionalArgument('--ansi', $config['ansi']);
        $arguments->addOptionalArgument('--no-ansi', $config['no-ansi']);

        return $arguments;
    }
}
