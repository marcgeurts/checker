<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Formatter\ProcessFormatterInterface;
use ClickNow\Checker\Process\ArgumentsCollection;
use ClickNow\Checker\Process\ProcessBuilder;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Result\Result;

abstract class AbstractExternalTask extends AbstractTask
{
    /**
     * @var \ClickNow\Checker\Process\ProcessBuilder
     */
    protected $processBuilder;

    /**
     * @var \ClickNow\Checker\Formatter\ProcessFormatterInterface
     */
    protected $processFormatter;

    /**
     * Abstract external task.
     *
     * @param \ClickNow\Checker\Process\ProcessBuilder              $processBuilder
     * @param \ClickNow\Checker\Formatter\ProcessFormatterInterface $processFormatter
     */
    public function __construct(ProcessBuilder $processBuilder, ProcessFormatterInterface $processFormatter)
    {
        $this->processBuilder = $processBuilder;
        $this->processFormatter = $processFormatter;
    }

    /**
     * Get command name.
     *
     * @return string
     */
    protected function getCommandName()
    {
        return $this->getName();
    }

    /**
     * Add arguments.
     *
     * @param \ClickNow\Checker\Process\ArgumentsCollection $arguments
     * @param array                                         $config
     * @param \ClickNow\Checker\Repository\FilesCollection  $files
     *
     * @return void
     */
    abstract protected function addArguments(ArgumentsCollection $arguments, array $config, FilesCollection $files);

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
    protected function execute(
        array $config,
        FilesCollection $files,
        CommandInterface $command,
        ContextInterface $context
    ) {
        $arguments = $this->processBuilder->createArgumentsForCommand($this->getCommandName());
        $this->addArguments($arguments, $config, $files);
        $process = $this->processBuilder->buildProcess($arguments, $command);
        $process->run();

        if (!$process->isSuccessful()) {
            return Result::error($command, $context, $this, $this->processFormatter->format($process));
        }

        return Result::success($command, $context, $this);
    }
}
