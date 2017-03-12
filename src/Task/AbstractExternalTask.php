<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Formatter\ProcessFormatterInterface;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Process\ProcessBuilder;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Repository\Filesystem;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\Process\Process;

abstract class AbstractExternalTask extends AbstractTask
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    protected $io;

    /**
     * @var \ClickNow\Checker\Repository\Filesystem
     */
    protected $filesystem;

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
     * @param \ClickNow\Checker\IO\IOInterface                      $io
     * @param \ClickNow\Checker\Repository\Filesystem               $filesystem
     * @param \ClickNow\Checker\Process\ProcessBuilder              $processBuilder
     * @param \ClickNow\Checker\Formatter\ProcessFormatterInterface $processFormatter
     */
    public function __construct(
        IOInterface $io,
        Filesystem $filesystem,
        ProcessBuilder $processBuilder,
        ProcessFormatterInterface $processFormatter
    ) {
        $this->io = $io;
        $this->filesystem = $filesystem;
        $this->processBuilder = $processBuilder;
        $this->processFormatter = $processFormatter;
    }

    /**
     * Create arguments.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     *
     * @return \ClickNow\Checker\Process\ArgumentsCollection
     */
    abstract protected function createArguments(array $config, FilesCollection $files);

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
    protected function execute(
        array $config,
        FilesCollection $files,
        RunnerInterface $runner,
        ContextInterface $context
    ) {
        $process = $this->buildProcess($config, $files, $runner);
        $process->run();

        if (!$this->isSuccessful($process)) {
            return Result::error($runner, $context, $this, $this->processFormatter->format($process));
        }

        return Result::success($runner, $context, $this);
    }

    /**
     * Build process.
     *
     * @param array                                        $config
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param \ClickNow\Checker\Runner\RunnerInterface     $runner
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function buildProcess(array $config, FilesCollection $files, RunnerInterface $runner)
    {
        $arguments = $this->createArguments($config, $files);

        return $this->processBuilder->buildProcess($arguments, $runner);
    }

    /**
     * Is successful?
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return bool
     */
    protected function isSuccessful(Process $process)
    {
        return $process->isSuccessful();
    }
}
