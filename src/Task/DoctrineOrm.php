<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class DoctrineOrm extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'doctrine-orm';
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
            'skip-mapping' => false,
            'skip-sync'    => false,
            'finder'       => ['extensions' => ['php', 'xml', 'yml']],
        ]);

        $resolver->addAllowedTypes('skip-mapping', ['bool']);
        $resolver->addAllowedTypes('skip-sync', ['bool']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('doctrine');
        $arguments->add('orm:validate-schema');
        $arguments->addOptionalArgument('--skip-mapping', $config['skip-mapping']);
        $arguments->addOptionalArgument('--skip-sync', $config['skip-sync']);

        return $arguments;
    }
}
