<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Formatter\ProcessFormatterInterface;
use ClickNow\Checker\Process\ProcessBuilder;

abstract class AbstractTask implements TaskInterface
{
    /**
     * @var \ClickNow\Checker\Config\Checker
     */
    protected $checker;

    /**
     * @var \ClickNow\Checker\Process\ProcessBuilder
     */
    protected $processBuilder;

    /**
     * @var \ClickNow\Checker\Formatter\ProcessFormatterInterface
     */
    protected $formatter;

    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * Abstract task.
     *
     * @param \ClickNow\Checker\Config\Checker                      $checker
     * @param \ClickNow\Checker\Process\ProcessBuilder              $processBuilder
     * @param \ClickNow\Checker\Formatter\ProcessFormatterInterface $formatter
     */
    public function __construct(Checker $checker, ProcessBuilder $processBuilder, ProcessFormatterInterface $formatter)
    {
        $this->checker = $checker;
        $this->processBuilder = $processBuilder;
        $this->formatter = $formatter;
    }

    /**
     * Merge default config.
     *
     * @param array $config
     */
    public function mergeDefaultConfig(array $config)
    {
        $this->defaultConfig = array_merge($this->defaultConfig, $config);
    }

    /**
     * Get config.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     *
     * @return array
     */
    public function getConfig(CommandInterface $command)
    {
        $config = $command->getActionConfig($this);

        $resolver = $this->getConfigOptions();
        $resolver->setDefaults($this->defaultConfig);

        return $resolver->resolve($config);
    }

    /**
     * This action can run in context?
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    public function canRunInContext(CommandInterface $command, ContextInterface $context)
    {
        return true;
    }

    /**
     * Run this action.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    public function run(CommandInterface $command, ContextInterface $context)
    {
        $config = $this->getConfig($command);

        return $this->execute($config, $command, $context);
    }

    /**
     * Execute this action with configuration.
     *
     * @param array                                      $config
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return \ClickNow\Checker\Result\ResultInterface
     */
    abstract protected function execute($config, CommandInterface $command, ContextInterface $context);
}
