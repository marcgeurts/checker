<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractTask implements TaskInterface
{
    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * Merge default config.
     *
     * @param array $config
     *
     * @return void
     */
    public function mergeDefaultConfig(array $config)
    {
        $this->defaultConfig = array_merge($this->defaultConfig, $config);
    }

    /**
     * Can run in context?
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(RunnerInterface $runner, ContextInterface $context)
    {
        $config = $this->getConfig($runner);
        $option = isset($config['can-run-in']) ? $config['can-run-in'] : true;

        if (is_array($option)) {
            return in_array($context->getRunner()->getName(), $option) || in_array($runner->getName(), $option);
        }

        return (bool) $option;
    }

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(RunnerInterface $runner, ContextInterface $context)
    {
        $config = $this->getConfig($runner);
        $finder = $this->getFinder(isset($config['finder']) ? (array) $config['finder'] : []);
        $files = $this->finderFiles($context->getFiles(), $finder);

        if ($this->isSkipped($files, $config)) {
            return Result::skipped($runner, $context, $this);
        }

        return $this->execute($config, $files, $runner, $context);
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'can-run-in'     => true,
            'always-execute' => false,
            'finder'         => [],
        ]);

        $resolver->addAllowedTypes('can-run-in', ['array', 'bool']);
        $resolver->addAllowedTypes('always-execute', ['bool']);
        $resolver->addAllowedTypes('finder', ['array']);

        return $resolver;
    }

    /**
     * Get config.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return array
     */
    protected function getConfig(RunnerInterface $runner)
    {
        $config = $runner->getActionConfig($this);

        $resolver = $this->getConfigOptions();
        $resolver->setDefaults($this->defaultConfig);

        return $resolver->resolve($config);
    }

    /**
     * Get finder.
     *
     * @param array $finder
     *
     * @return array
     */
    protected function getFinder(array $finder)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'name'       => [],
            'not-name'   => [],
            'path'       => [],
            'not-path'   => [],
            'extensions' => [],
        ]);

        $resolver->addAllowedTypes('name', ['array', 'string']);
        $resolver->addAllowedTypes('not-name', ['array', 'string']);
        $resolver->addAllowedTypes('path', ['array', 'string']);
        $resolver->addAllowedTypes('not-path', ['array', 'string']);
        $resolver->addAllowedTypes('extensions', ['array', 'string']);

        return $resolver->resolve($finder);
    }

    /**
     * Finder files.
     *
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param array                                        $finder
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    protected function finderFiles(FilesCollection $files, array $finder)
    {
        return $files
            ->filterByName($finder['name'])
            ->filterByNotName($finder['not-name'])
            ->filterByPath($finder['path'])
            ->filterByNotPath($finder['not-path'])
            ->filterByExtensions((array) $finder['extensions']);
    }

    /**
     * Is skipped?
     *
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param array                                        $config
     *
     * @return bool
     */
    protected function isSkipped(FilesCollection $files, array $config)
    {
        $alwaysExecute = isset($config['always-execute']) ? $config['always-execute'] : false;

        return !$alwaysExecute && $files->isEmpty();
    }

    /**
     * Execute.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param \ClickNow\Checker\Runner\RunnerInterface     $runner
     * @param \ClickNow\Checker\Context\ContextInterface   $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    abstract protected function execute(
        array $config,
        FilesCollection $files,
        RunnerInterface $runner,
        ContextInterface $context
    );
}
