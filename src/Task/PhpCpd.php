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

        $resolver->setDefaults([
            'paths'      => '.',
            'min-lines'  => null,
            'min-tokens' => null,
            'fuzzy'      => false,
            'finder'     => [
                'name'     => ['*.php'],
                'not-path' => ['vendor'],
            ],
        ]);

        $resolver->addAllowedTypes('paths', ['string', 'array']);
        $resolver->addAllowedTypes('min-lines', ['null', 'int']);
        $resolver->addAllowedTypes('min-tokens', ['null', 'int']);
        $resolver->addAllowedTypes('fuzzy', ['bool']);

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
        $arguments->addArgumentArray('%s', (array) $config['paths']);
        $arguments->addOptionalCommaSeparatedArgument('--names=%s', $config['finder']['name']);
        $arguments->addArgumentArray('--names-exclude=%s', $config['finder']['not-name']);
        $arguments->addArgumentArray('--exclude=%s', $config['finder']['not-path']);
        $arguments->addOptionalArgument('--min-lines=%u', $config['min-lines']);
        $arguments->addOptionalArgument('--min-tokens=%u', $config['min-tokens']);
        $arguments->addOptionalArgument('--fuzzy', $config['fuzzy']);

        return $arguments;
    }
}
