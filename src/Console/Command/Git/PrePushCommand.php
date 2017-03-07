<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Command\AbstractRunnerCommand;
use ClickNow\Checker\Context\Git\PrePushContext;
use ClickNow\Checker\Repository\Git;
use ClickNow\Checker\Runner\RunnerInterface;

class PrePushCommand extends AbstractRunnerCommand
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
     * Pre push command.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param \ClickNow\Checker\Repository\Git         $git
     */
    public function __construct(RunnerInterface $runner, Git $git)
    {
        $this->runner = $runner;
        $this->git = $git;

        parent::__construct('git:pre-push');

        $this->setAliases(['git:prepush', 'git:pp']);
        $this->setDescription('Git hook pre-push.');
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
            $this->git->getCommittedFiles($this->consoleIO()->readCommandInput(STDIN))
        );
    }
}
