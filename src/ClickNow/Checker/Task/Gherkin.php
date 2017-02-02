<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Repository\FilesCollection;

class Gherkin extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'gherkin';
    }

    /**
     * Get command name.
     *
     * @return string
     */
    protected function getCommandName()
    {
        return 'kawaii';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // align
        $resolver->setDefault('align', null);
        $resolver->addAllowedTypes('align', ['null', 'string']);
        $resolver->addAllowedValues('align', [null, 'left', 'right']);

        // directory
        $resolver->setDefault('directory', 'features');
        $resolver->addAllowedTypes('directory', ['string']);

        return $resolver;
    }

    /**
     * Add arguments.
     *
     * @param \ClickNow\Checker\Process\ArgumentsCollection $arguments
     * @param array                                         $config
     * @param \ClickNow\Checker\Repository\FilesCollection  $files
     */
    protected function addArguments(ArgumentsCollection $arguments, array $config, FilesCollection $files)
    {
        $arguments->add('gherkin:check');
        $arguments->addOptionalArgument('--align=%s', $config['align']);
        $arguments->add($config['directory']);
    }
}
