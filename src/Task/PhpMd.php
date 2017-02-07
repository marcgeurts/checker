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

        $resolver->setDefaults([
            'ruleset'          => ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode'],
            'minimum-priority' => null,
            'strict'           => false,
            'finder'           => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('ruleset', ['string', 'array']);
        $resolver->addAllowedTypes('minimum-priority', ['null', 'int']);
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
        $arguments->addOptionalArgument('--strict', $config['strict']);

        return $arguments;
    }
}
