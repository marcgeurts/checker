<?php

namespace ClickNow\Checker\Context\Git;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class PreCommitContext implements ContextInterface
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface
     */
    private $runner;

    /**
     * @var \ClickNow\Checker\Repository\FilesCollection
     */
    private $files;

    /**
     * Git pre commit context.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface     $runner
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     */
    public function __construct(RunnerInterface $runner, FilesCollection $files)
    {
        $this->runner = $runner;
        $this->files = $files;
    }

    /**
     * Get runner.
     *
     * @return \ClickNow\Checker\Runner\RunnerInterface
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Get files.
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function getFiles()
    {
        return $this->files;
    }
}
