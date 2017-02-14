<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class PhpUnit extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'PHPUnit';
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
            'coverage-clover'      => null,
            'coverage-crap4j'      => null,
            'coverage-html'        => null,
            'coverage-php'         => null,
            'coverage-text'        => null,
            'coverage-xml'         => null,
            'log-junit'            => null,
            'log-tap'              => null,
            'log-json'             => null,
            'testdox-html'         => null,
            'testdox-text'         => null,
            'filter'               => null,
            'testsuite'            => null,
            'group'                => [],
            'exclude-group'        => [],
            'test-suffix'          => [],
            'report-useless-tests' => false,
            'strict-coverage'      => false,
            'strict-global-state'  => false,
            'disallow-test-output' => false,
            'enforce-time-limit'   => false,
            'disallow-todo-tests'  => false,
            'process-isolation'    => false,
            'no-globals-backup'    => false,
            'static-backup'        => false,
            'colors'               => null,
            'columns'              => null,
            'stderr'               => false,
            'stop-on-error'        => false,
            'stop-on-failure'      => $runner->isStopOnFailure(),
            'stop-on-risky'        => false,
            'stop-on-skipped'      => false,
            'stop-on-incomplete'   => false,
            'verbose'              => $this->io->isVerbose(),
            'debug'                => $this->io->isDebug(),
            'loader'               => null,
            'repeat'               => null,
            'tap'                  => false,
            'testdox'              => false,
            'printer'              => null,
            'bootstrap'            => null,
            'configuration'        => null,
            'no-configuration'     => false,
            'no-coverage'          => false,
            'include-path'         => null,
            'd'                    => [],
            'finder'               => ['extensions' => ['php']],
        ]);

        $resolver->addAllowedTypes('coverage-clover', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-crap4j', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-html', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-php', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-text', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-xml', ['null', 'string']);
        $resolver->addAllowedTypes('log-junit', ['null', 'string']);
        $resolver->addAllowedTypes('log-tap', ['null', 'string']);
        $resolver->addAllowedTypes('log-json', ['null', 'string']);
        $resolver->addAllowedTypes('testdox-html', ['null', 'string']);
        $resolver->addAllowedTypes('testdox-text', ['null', 'string']);
        $resolver->addAllowedTypes('filter', ['null', 'string']);
        $resolver->addAllowedTypes('testsuite', ['null', 'string']);
        $resolver->addAllowedTypes('group', ['array']);
        $resolver->addAllowedTypes('exclude-group', ['array']);
        $resolver->addAllowedTypes('test-suffix', ['array']);
        $resolver->addAllowedTypes('report-useless-tests', ['bool']);
        $resolver->addAllowedTypes('strict-coverage', ['bool']);
        $resolver->addAllowedTypes('strict-global-state', ['bool']);
        $resolver->addAllowedTypes('disallow-test-output', ['bool']);
        $resolver->addAllowedTypes('enforce-time-limit', ['bool']);
        $resolver->addAllowedTypes('disallow-todo-tests', ['bool']);
        $resolver->addAllowedTypes('process-isolation', ['bool']);
        $resolver->addAllowedTypes('no-globals-backup', ['bool']);
        $resolver->addAllowedTypes('static-backup', ['bool']);
        $resolver->addAllowedTypes('colors', ['null', 'string']);
        $resolver->addAllowedTypes('columns', ['null', 'int']);
        $resolver->addAllowedTypes('stderr', ['bool']);
        $resolver->addAllowedTypes('stop-on-error', ['bool']);
        $resolver->addAllowedTypes('stop-on-failure', ['bool']);
        $resolver->addAllowedTypes('stop-on-risky', ['bool']);
        $resolver->addAllowedTypes('stop-on-skipped', ['bool']);
        $resolver->addAllowedTypes('stop-on-incomplete', ['bool']);
        $resolver->addAllowedTypes('verbose', ['bool']);
        $resolver->addAllowedTypes('debug', ['bool']);
        $resolver->addAllowedTypes('loader', ['null', 'string']);
        $resolver->addAllowedTypes('repeat', ['null', 'int']);
        $resolver->addAllowedTypes('tap', ['bool']);
        $resolver->addAllowedTypes('testdox', ['bool']);
        $resolver->addAllowedTypes('printer', ['null', 'string']);
        $resolver->addAllowedTypes('bootstrap', ['null', 'string']);
        $resolver->addAllowedTypes('configuration', ['null', 'string']);
        $resolver->addAllowedTypes('no-configuration', ['bool']);
        $resolver->addAllowedTypes('no-coverage', ['bool']);
        $resolver->addAllowedTypes('include-path', ['null', 'string']);

        $resolver->addAllowedValues('colors', [null, 'never', 'auto', 'always']);

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
        $arguments = $this->processBuilder->createArgumentsForCommand('phpunit');
        $arguments->addOptionalArgument('--coverage-clover=%s', $config['coverage-clover']);
        $arguments->addOptionalArgument('--coverage-crap4j=%s', $config['coverage-crap4j']);
        $arguments->addOptionalArgument('--coverage-html=%s', $config['coverage-html']);
        $arguments->addOptionalArgument('--coverage-php=%s', $config['coverage-php']);
        $arguments->addOptionalArgument('--coverage-text=%s', $config['coverage-text']);
        $arguments->addOptionalArgument('--coverage-xml=%s', $config['coverage-xml']);
        $arguments->addOptionalArgument('--log-junit=%s', $config['log-junit']);
        $arguments->addOptionalArgument('--log-tap=%s', $config['log-tap']);
        $arguments->addOptionalArgument('--log-json=%s', $config['log-json']);
        $arguments->addOptionalArgument('--testdox-html=%s', $config['testdox-html']);
        $arguments->addOptionalArgument('--testdox-text=%s', $config['testdox-text']);
        $arguments->addOptionalArgument('--filter=%s', $config['filter']);
        $arguments->addOptionalArgument('--testsuite=%s', $config['testsuite']);
        $arguments->addOptionalCommaSeparatedArgument('--group=%s', $config['group']);
        $arguments->addOptionalCommaSeparatedArgument('--exclude-group=%s', $config['exclude-group']);
        $arguments->addOptionalCommaSeparatedArgument('--test-suffix=%s', $config['test-suffix']);
        $arguments->addOptionalArgument('--report-useless-tests', $config['report-useless-tests']);
        $arguments->addOptionalArgument('--strict-coverage', $config['strict-coverage']);
        $arguments->addOptionalArgument('--strict-global-state', $config['strict-global-state']);
        $arguments->addOptionalArgument('--disallow-test-output', $config['disallow-test-output']);
        $arguments->addOptionalArgument('--enforce-time-limit', $config['enforce-time-limit']);
        $arguments->addOptionalArgument('--disallow-todo-tests', $config['disallow-todo-tests']);
        $arguments->addOptionalArgument('--process-isolation', $config['process-isolation']);
        $arguments->addOptionalArgument('--no-globals-backup', $config['no-globals-backup']);
        $arguments->addOptionalArgument('--static-backup', $config['static-backup']);
        $arguments->addOptionalArgument('--colors=%s', $config['colors']);
        $arguments->addOptionalArgument('--columns=%s', $config['columns']);
        $arguments->addOptionalArgument('--stderr', $config['stderr']);
        $arguments->addOptionalArgument('--stop-on-error', $config['stop-on-error']);
        $arguments->addOptionalArgument('--stop-on-failure', $config['stop-on-failure']);
        $arguments->addOptionalArgument('--stop-on-risky', $config['stop-on-risky']);
        $arguments->addOptionalArgument('--stop-on-skipped', $config['stop-on-skipped']);
        $arguments->addOptionalArgument('--stop-on-incomplete', $config['stop-on-incomplete']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addOptionalArgument('--debug', $config['debug']);
        $arguments->addOptionalArgument('--loader=%s', $config['loader']);
        $arguments->addOptionalArgument('--repeat=%s', $config['repeat']);
        $arguments->addOptionalArgument('--tap', $config['tap']);
        $arguments->addOptionalArgument('--testdox', $config['testdox']);
        $arguments->addOptionalArgument('--printer=%s', $config['printer']);
        $arguments->addOptionalArgument('--bootstrap=%s', $config['bootstrap']);
        $arguments->addOptionalArgument('--configuration=%s', $config['configuration']);
        $arguments->addOptionalArgument('--no-configuration', $config['no-configuration']);
        $arguments->addOptionalArgument('--no-coverage', $config['no-coverage']);
        $arguments->addOptionalArgument('--include-path=%s', $config['include-path']);

        return $arguments;
    }
}
