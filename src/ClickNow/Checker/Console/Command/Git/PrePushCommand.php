<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Command\AbstractRunnerCommand;
use ClickNow\Checker\Context\Git\PrePushContext;
use ClickNow\Checker\Repository\FinderFiles;
use ClickNow\Checker\Runner\RunnerInterface;

class PrePushCommand extends AbstractRunnerCommand
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface
     */
    private $runner;

    /**
     * Pre push command.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param \ClickNow\Checker\Repository\FinderFiles $finderFiles
     */
    public function __construct(RunnerInterface $runner, FinderFiles $finderFiles)
    {
        $this->runner = $runner;

        parent::__construct($finderFiles, 'git:pre-push', 'Git hook pre-push.');
    }

    /**
     * Context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    protected function context()
    {
        return new PrePushContext(
            $this->runner,
            $this->finderFiles->getCommittedFiles($this->consoleIO()->readCommandInput(STDIN))
        );
    }
}
