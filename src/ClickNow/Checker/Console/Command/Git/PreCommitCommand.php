<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Command\AbstractRunnerCommand;
use ClickNow\Checker\Context\Git\PreCommitContext;
use ClickNow\Checker\Repository\FinderFiles;
use ClickNow\Checker\Runner\RunnerInterface;

class PreCommitCommand extends AbstractRunnerCommand
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface
     */
    private $runner;

    /**
     * Pre commit command.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param \ClickNow\Checker\Repository\FinderFiles $finderFiles
     */
    public function __construct(RunnerInterface $runner, FinderFiles $finderFiles)
    {
        $this->runner = $runner;

        parent::__construct($finderFiles, 'git:pre-commit', 'Git hook pre-commit.');
    }

    /**
     * Context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    protected function context()
    {
        return new PreCommitContext(
            $this->runner,
            $this->finderFiles->getChangedFiles($this->consoleIO()->readCommandInput(STDIN))
        );
    }
}
