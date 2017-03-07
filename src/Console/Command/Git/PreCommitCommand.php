<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Command\AbstractRunnerCommand;
use ClickNow\Checker\Context\Git\PreCommitContext;
use ClickNow\Checker\Repository\Git;
use ClickNow\Checker\Runner\RunnerInterface;

class PreCommitCommand extends AbstractRunnerCommand
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface
     */
    private $runner;

    /**
     * @var \ClickNow\Checker\Repository\Git
     */
    private $git;

    /**
     * Pre commit command.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param \ClickNow\Checker\Repository\Git         $git
     */
    public function __construct(RunnerInterface $runner, Git $git)
    {
        $this->runner = $runner;
        $this->git = $git;

        parent::__construct('git:pre-commit');

        $this->setAliases(['git:pre-commit', 'git:pc']);
        $this->setDescription('Git hook pre-commit.');
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
            $this->git->getChangedFiles($this->consoleIO()->readCommandInput(STDIN))
        );
    }
}
