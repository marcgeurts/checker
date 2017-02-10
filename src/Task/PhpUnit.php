<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class PhpUnit extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHPUnit';
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
            'configuration' => null,
            'group'         => [],
            'finder'        => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('configuration', ['null', 'string']);
        $resolver->addAllowedTypes('group', ['array']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('phpunit');
        $arguments->addOptionalArgument('--configuration=%s', $config['configuration']);
        $arguments->addOptionalCommaSeparatedArgument('--group=%s', $config['group']);

        return $arguments;
    }
}
