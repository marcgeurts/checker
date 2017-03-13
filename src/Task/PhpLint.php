<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class PhpLint extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHPLint';
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
            'php'            => null,
            'short-open-tag' => false,
            'asp-tags'       => false,
            'exclude'        => [],
            'jobs'           => null,
            'no-colors'      => true,
            'blame'          => false,
            'git'            => null,
            'ignore-fails'   => false,
            'finder'         => ['extensions' => ['php', 'php3', 'php4', 'php5', 'phtm']],
        ]);

        $resolver->addAllowedTypes('php', ['null', 'string']);
        $resolver->addAllowedTypes('short-open-tag', ['bool']);
        $resolver->addAllowedTypes('asp-tags', ['bool']);
        $resolver->addAllowedTypes('exclude', ['array']);
        $resolver->addAllowedTypes('jobs', ['null', 'int']);
        $resolver->addAllowedTypes('no-colors', ['bool']);
        $resolver->addAllowedTypes('blame', ['bool']);
        $resolver->addAllowedTypes('git', ['null', 'string']);
        $resolver->addAllowedTypes('ignore-fails', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('parallel-lint');
        $arguments->addOptionalArgumentWithSeparatedValue('-p', $config['php']);
        $arguments->addOptionalArgument('--short', $config['short-open-tag']);
        $arguments->addOptionalArgument('-asp', $config['asp-tags']);
        $arguments->addArgumentArrayWithSeparatedValue('--exclude', $config['exclude']);
        $arguments->addOptionalArgumentWithSeparatedValue('-j', $config['jobs']);
        $arguments->addOptionalArgument('--no-colors', $config['no-colors']);
        $arguments->addOptionalArgument('--blame', $config['blame']);
        $arguments->addOptionalArgumentWithSeparatedValue('--git', $config['git']);
        $arguments->addOptionalArgument('--ignore-fails', $config['ignore-fails']);
        $arguments->addFiles($files);

        return $arguments;
    }
}
