<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class PhpMd extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHP Mess Detector (phpmd)';
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
            'ruleset'          => ['cleancode', 'codesize', 'controversial', 'design', 'naming', 'unusedcode'],
            'minimum-priority' => null,
            'strict'           => false,
            'coverage'         => null,
            'reportfile'       => null,
            'reportfile-html'  => null,
            'reportfile-text'  => null,
            'reportfile-xml'   => null,
            'finder'           => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('ruleset', ['string', 'array']);
        $resolver->addAllowedTypes('minimum-priority', ['null', 'int']);
        $resolver->addAllowedTypes('strict', ['bool']);
        $resolver->addAllowedTypes('coverage', ['null', 'string']);
        $resolver->addAllowedTypes('reportfile', ['null', 'string']);
        $resolver->addAllowedTypes('reportfile-html', ['null', 'string']);
        $resolver->addAllowedTypes('reportfile-text', ['null', 'string']);
        $resolver->addAllowedTypes('reportfile-xml', ['null', 'string']);

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
        $arguments->addOptionalArgumentWithSeparatedValue('--coverage', $config['coverage']);
        $arguments->addOptionalArgumentWithSeparatedValue('--reportfile', $config['reportfile']);
        $arguments->addOptionalArgumentWithSeparatedValue('--reportfile-html', $config['reportfile-html']);
        $arguments->addOptionalArgumentWithSeparatedValue('--reportfile-text', $config['reportfile-text']);
        $arguments->addOptionalArgumentWithSeparatedValue('--reportfile-xml', $config['reportfile-xml']);

        return $arguments;
    }
}
