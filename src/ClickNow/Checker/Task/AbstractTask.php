<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Result\Result;
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
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(CommandInterface $command, ContextInterface $context)
    {
        $config = $this->getConfig($command);
        $option = isset($config['can_run_in']) ? $config['can_run_in'] : true;

        if (is_array($option)) {
            return in_array($context->getCommand()->getName(), $option) || in_array($command->getName(), $option);
        }

        return (bool) $option;
    }

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(CommandInterface $command, ContextInterface $context)
    {
        $config = $this->getConfig($command);
        $finder = $this->getFinder(isset($config['finder']) ? (array) $config['finder'] : []);
        $files = $this->finderFiles($context->getFiles(), $finder);

        if ($this->isSkipped($files, $config)) {
            return Result::skipped($command, $context, $this);
        }

        return $this->execute($config, $files, $command, $context);
    }

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected function getConfigOptions()
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'can_run_in'     => true,
            'always_execute' => false,
            'finder'         => [],
        ]);

        $resolver->addAllowedTypes('can_run_in', ['array', 'bool']);
        $resolver->addAllowedTypes('always_execute', ['bool']);
        $resolver->addAllowedTypes('finder', ['array']);

        return $resolver;
    }

    /**
     * Get config.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     *
     * @return array
     */
    protected function getConfig(CommandInterface $command)
    {
        $config = $command->getActionConfig($this);

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
            'not_name'   => [],
            'path'       => [],
            'not_path'   => [],
            'extensions' => [],
        ]);

        $resolver->addAllowedTypes('name', ['array', 'string']);
        $resolver->addAllowedTypes('not_name', ['array', 'string']);
        $resolver->addAllowedTypes('path', ['array', 'string']);
        $resolver->addAllowedTypes('not_path', ['array', 'string']);
        $resolver->addAllowedTypes('extensions', ['array']);

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
            ->filterByNotName($finder['not_name'])
            ->filterByPath($finder['path'])
            ->filterByNotPath($finder['not_path'])
            ->filterByExtensions($finder['extensions']);
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
        $alwaysExecute = isset($config['always_execute']) ? $config['always_execute'] : false;

        return !$alwaysExecute && $files->isEmpty();
    }

    /**
     * Execute.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param \ClickNow\Checker\Command\CommandInterface   $command
     * @param \ClickNow\Checker\Context\ContextInterface   $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    abstract protected function execute(
        array $config,
        FilesCollection $files,
        CommandInterface $command,
        ContextInterface $context
    );
}
