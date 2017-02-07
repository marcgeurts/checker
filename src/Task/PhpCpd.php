<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class PhpCpd extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'phpcpd';
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        // values
        $resolver->setDefault('values', '.');
        $resolver->addAllowedTypes('values', ['string', 'array']);

        // min-lines
        $resolver->setDefault('min-lines', null);
        $resolver->addAllowedTypes('min-lines', ['null', 'int']);

        // min-tokens
        $resolver->setDefault('min-tokens', null);
        $resolver->addAllowedTypes('min-tokens', ['null', 'int']);

        // fuzzy
        $resolver->setDefault('fuzzy', false);
        $resolver->addAllowedTypes('fuzzy', ['bool']);

        // finder
        $resolver->setDefault('finder', ['name' => ['*.php']]);

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
        $arguments->addArgumentArray('%s', (array) $config['values']);
        $arguments->addOptionalCommaSeparatedArgument('--names=%s', $config['finder']['name']);
        $arguments->addArgumentArray('--names-exclude=%s', $config['finder']['not-name']);
        $arguments->addArgumentArray('--exclude=%s', $config['finder']['not-path']);
        $arguments->addOptionalArgument('--min-lines=%u', $config['min-lines']);
        $arguments->addOptionalArgument('--min-tokens=%u', $config['min-tokens']);
        $arguments->addOptionalArgument('--fuzzy', $config['fuzzy']);

        return $arguments;
    }
}
