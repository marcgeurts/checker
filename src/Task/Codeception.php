<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;

class Codeception extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'codeception';
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
            'override'   => [],
            'config'     => null,
            'report'     => false,
            'silent'     => false,
            'steps'      => false,
            'debug'      => false,
            'group'      => [],
            'skip'       => [],
            'skip-group' => [],
            'env'        => [],
            'fail-fast'  => false,
            'suite'      => null,
            'test'       => null,
        ]);

        $resolver->addAllowedTypes('override', ['array']);
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('report', ['bool']);
        $resolver->addAllowedTypes('silent', ['bool']);
        $resolver->addAllowedTypes('steps', ['bool']);
        $resolver->addAllowedTypes('debug', ['bool']);
        $resolver->addAllowedTypes('group', ['array']);
        $resolver->addAllowedTypes('skip', ['array']);
        $resolver->addAllowedTypes('skip-group', ['array']);
        $resolver->addAllowedTypes('env', ['array']);
        $resolver->addAllowedTypes('fail-fast', ['bool']);
        $resolver->addAllowedTypes('suite', ['null', 'string']);
        $resolver->addAllowedTypes('test', ['null', 'string']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('codecept');
        $arguments->add('run');
        $arguments->addOptionalArgument('--config=%s', $config['config']);
        $arguments->addArgumentArray('--override=%s', $config['override']);
        $arguments->addOptionalArgument('--report', $config['report']);
        $arguments->addOptionalArgument('--silent', $config['silent']);
        $arguments->addOptionalArgument('--steps', $config['steps']);
        $arguments->addOptionalArgument('--debug', $config['debug']);
        $arguments->addArgumentArray('--group=%s', $config['group']);
        $arguments->addArgumentArray('--skip=%s', $config['skip']);
        $arguments->addArgumentArray('--skip-group=%s', $config['skip-group']);
        $arguments->addOptionalCommaSeparatedArgument('--env=%s', $config['env']);
        $arguments->addOptionalArgument('--fail-fast', $config['fail-fast']);
        $arguments->addOptionalArgument('%s', $config['suite']);
        $arguments->addOptionalArgument('%s', $config['test']);

        return $arguments;
    }
}
