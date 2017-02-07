<?php

namespace ClickNow\Checker\Task;

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
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = parent::getConfigOptions();

        $resolver->setDefaults([
            'align'     => null,
            'directory' => 'features',
            'finder'    => ['extensions' => ['feature']],
        ]);

        $resolver->addAllowedTypes('align', ['null', 'string']);
        $resolver->addAllowedValues('align', [null, 'left', 'right']);
        $resolver->addAllowedTypes('directory', ['string']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('kawaii');
        $arguments->add('gherkin:check');
        $arguments->addOptionalArgument('--align=%s', $config['align']);
        $arguments->add($config['directory']);

        return $arguments;
    }
}
