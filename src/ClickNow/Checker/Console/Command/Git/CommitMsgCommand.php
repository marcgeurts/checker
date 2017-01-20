<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Command\AbstractRunnerCommand;
use ClickNow\Checker\Context\Git\CommitMsgContext;
use ClickNow\Checker\Repository\FinderFiles;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CommitMsgCommand extends AbstractRunnerCommand
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface
     */
    private $runner;

    /**
     * Commit msg command.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param \ClickNow\Checker\Repository\FinderFiles $finderFiles
     */
    public function __construct(RunnerInterface $runner, FinderFiles $finderFiles)
    {
        $this->runner = $runner;

        parent::__construct($finderFiles, 'git:commit-msg', 'Git hook commit-msg.');

        $this->addArgument('commit-message-file', InputArgument::OPTIONAL, 'The configured commit message file.');
        $this->addOption('git-user-name', null, InputOption::VALUE_REQUIRED, 'The configured git user name.');
        $this->addOption('git-user-email', null, InputOption::VALUE_REQUIRED, 'The configured git use email.');
    }

    /**
     * Context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    protected function context()
    {
        $commitMessageFile = $this->input->hasArgument('commit-message-file')
            ? $this->input->getArgument('commit-message-file')
            : $this->paths()->getGitDir().'.git/COMMIT_EDITMSG';

        return new CommitMsgContext(
            $this->runner,
            $this->finderFiles->getChangedFiles($this->consoleIO()->readCommandInput(STDIN)),
            file_exists($commitMessageFile) ? file_get_contents($commitMessageFile) : null,
            $this->input->getOption('git-user-name') ?: $this->finderFiles->getUserName(),
            $this->input->getOption('git-user-email') ?: $this->finderFiles->getUserEmail()
        );
    }
}
