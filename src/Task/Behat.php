<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class Behat extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'behat';
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
            'config' => null,
            'format' => [],
            'suite'  => null,
            'finder' => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('format', ['array']);
        $resolver->addAllowedTypes('suite', ['null', 'string']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('behat');
        $arguments->addOptionalArgumentWithSeparatedValue('--config', $config['config']);
        $arguments->addArgumentArray('--format=%s', $config['format']);
        $arguments->addOptionalArgument('--suite=%s', $config['suite']);

        return $arguments;
    }
}
