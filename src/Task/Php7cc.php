<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class Php7cc extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'php7cc';
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
            'level'  => null,
            'finder' => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('level', ['null', 'string']);
        $resolver->addAllowedValues('level', [null, 'info', 'warning', 'error']);

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
        $arguments->addFiles($files);

        return $arguments;
    }
}
