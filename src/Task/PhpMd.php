<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class PhpMd extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'phpmd';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // ruleset
        $resolver->setDefault('ruleset', ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode']);
        $resolver->addAllowedTypes('ruleset', ['string', 'array']);

        // minimum-priority
        $resolver->setDefault('minimum-priority', null);
        $resolver->addAllowedTypes('minimum-priority', ['null', 'int']);

        // exclude
        $resolver->setDefault('exclude', []);
        $resolver->addAllowedTypes('exclude', ['array']);

        // strict
        $resolver->setDefault('strict', false);
        $resolver->addAllowedTypes('strict', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('phpmd');
        $arguments->addCommaSeparatedFiles($files);
        $arguments->add('text');
        $arguments->addOptionalCommaSeparatedArgument('%s', (array) $config['ruleset']);
        $arguments->addOptionalArgumentWithSeparatedValue('--minimumpriority', $config['minimum-priority']);
        $arguments->addOptionalArgument('--exclude', !empty($config['exclude']));
        $arguments->addOptionalCommaSeparatedArgument('%s', $config['exclude']);
        $arguments->addOptionalArgument('--strict', $config['strict']);

        return $arguments;
    }
}
