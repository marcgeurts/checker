<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Command\AbstractRunnerCommand;
use ClickNow\Checker\Context\Git\CommitMsgContext;
use ClickNow\Checker\Repository\Git;
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
     * @var \ClickNow\Checker\Repository\Git
     */
    private $git;

    /**
     * Commit msg command.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param \ClickNow\Checker\Repository\Git         $git
     */
    public function __construct(RunnerInterface $runner, Git $git)
    {
        $this->runner = $runner;
        $this->git = $git;

        parent::__construct('git:commit-msg');

        $this->setDescription('Git hook commit-msg.');
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
            $this->git->getChangedFiles($this->consoleIO()->readCommandInput(STDIN)),
            file_exists($commitMessageFile) ? file_get_contents($commitMessageFile) : null,
            $this->input->getOption('git-user-name') ?: $this->git->getUserName(),
            $this->input->getOption('git-user-email') ?: $this->git->getUserEmail()
        );
    }
}
