<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class Codeception extends AbstractExternalTask
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Codeception';
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
            'suite'           => null,
            'test'            => null,
            'override'        => [],
            'config'          => null,
            'report'          => false,
            'html'            => null,
            'xml'             => null,
            'tap'             => null,
            'json'            => null,
            'colors'          => false,
            'no-colors'       => false,
            'silent'          => false,
            'steps'           => false,
            'debug'           => $this->io->isDebug(),
            'coverage'        => null,
            'coverage-html'   => null,
            'coverage-xml'    => null,
            'coverage-text'   => null,
            'coverage-crap4j' => null,
            'group'           => [],
            'skip'            => [],
            'skip-group'      => [],
            'env'             => [],
            'fail-fast'       => $runner->isStopOnFailure(),
            'no-rebuild'      => false,
            'quiet'           => $this->io->isQuiet(),
            'verbose'         => $this->io->isVerbose(),
            'ansi'            => $this->io->isDecorated(),
            'no-ansi'         => !$this->io->isDecorated(),
        ]);

        $resolver->addAllowedTypes('suite', ['null', 'string']);
        $resolver->addAllowedTypes('test', ['null', 'string']);
        $resolver->addAllowedTypes('override', ['array']);
        $resolver->addAllowedTypes('config', ['null', 'string']);
        $resolver->addAllowedTypes('report', ['bool']);
        $resolver->addAllowedTypes('html', ['null', 'string']);
        $resolver->addAllowedTypes('xml', ['null', 'string']);
        $resolver->addAllowedTypes('tap', ['null', 'string']);
        $resolver->addAllowedTypes('json', ['null', 'string']);
        $resolver->addAllowedTypes('colors', ['bool']);
        $resolver->addAllowedTypes('no-colors', ['bool']);
        $resolver->addAllowedTypes('silent', ['bool']);
        $resolver->addAllowedTypes('steps', ['bool']);
        $resolver->addAllowedTypes('debug', ['bool']);
        $resolver->addAllowedTypes('coverage', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-html', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-xml', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-text', ['null', 'string']);
        $resolver->addAllowedTypes('coverage-crap4j', ['null', 'string']);
        $resolver->addAllowedTypes('group', ['array']);
        $resolver->addAllowedTypes('skip', ['array']);
        $resolver->addAllowedTypes('skip-group', ['array']);
        $resolver->addAllowedTypes('env', ['array']);
        $resolver->addAllowedTypes('fail-fast', ['bool']);
        $resolver->addAllowedTypes('no-rebuild', ['bool']);
        $resolver->addAllowedTypes('quiet', ['bool']);
        $resolver->addAllowedTypes('verbose', ['bool']);
        $resolver->addAllowedTypes('ansi', ['bool']);
        $resolver->addAllowedTypes('no-ansi', ['bool']);

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
        $arguments->addOptionalArgument('%s', $config['suite']);
        $arguments->addOptionalArgument('%s', $config['test']);
        $arguments->addArgumentArray('--override=%s', $config['override']);
        $arguments->addOptionalArgument('--config=%s', $config['config']);
        $arguments->addOptionalArgument('--report', $config['report']);
        $arguments->addOptionalArgument('--html=%s', $config['html']);
        $arguments->addOptionalArgument('--xml=%s', $config['xml']);
        $arguments->addOptionalArgument('--tap=%s', $config['tap']);
        $arguments->addOptionalArgument('--json=%s', $config['json']);
        $arguments->addOptionalArgument('--colors', $config['colors']);
        $arguments->addOptionalArgument('--no-colors', $config['no-colors']);
        $arguments->addOptionalArgument('--silent', $config['silent']);
        $arguments->addOptionalArgument('--steps', $config['steps']);
        $arguments->addOptionalArgument('--debug', $config['debug']);
        $arguments->addOptionalArgument('--coverage=%s', $config['coverage']);
        $arguments->addOptionalArgument('--coverage-html=%s', $config['coverage-html']);
        $arguments->addOptionalArgument('--coverage-xml=%s', $config['coverage-xml']);
        $arguments->addOptionalArgument('--coverage-text=%s', $config['coverage-text']);
        $arguments->addOptionalArgument('--coverage-crap4j=%s', $config['coverage-crap4j']);
        $arguments->addArgumentArray('--group=%s', $config['group']);
        $arguments->addArgumentArray('--skip=%s', $config['skip']);
        $arguments->addArgumentArray('--skip-group=%s', $config['skip-group']);
        $arguments->addOptionalCommaSeparatedArgument('--env=%s', $config['env']);
        $arguments->addOptionalArgument('--fail-fast', $config['fail-fast']);
        $arguments->addOptionalArgument('--no-rebuild', $config['no-rebuild']);
        $arguments->addOptionalArgument('--quiet', $config['quiet']);
        $arguments->addOptionalArgument('--verbose', $config['verbose']);
        $arguments->addOptionalArgument('--ansi', $config['ansi']);
        $arguments->addOptionalArgument('--no-ansi', $config['no-ansi']);

        return $arguments;
    }
}
