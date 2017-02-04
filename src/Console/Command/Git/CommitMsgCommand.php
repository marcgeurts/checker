<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Console\Command\AbstractRunnerCommand;
use ClickNow\Checker\Context\Git\CommitMsgContext;
use ClickNow\Checker\Repository\Filesystem;
use ClickNow\Checker\Repository\Git;
use ClickNow\Checker\Runner\RunnerInterface;
use SplFileInfo;
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
     * @var \ClickNow\Checker\Repository\Filesystem
     */
    private $filesystem;

    /**
     * Commit msg command.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param \ClickNow\Checker\Repository\Git         $git
     * @param \ClickNow\Checker\Repository\Filesystem  $filesystem
     */
    public function __construct(RunnerInterface $runner, Git $git, Filesystem $filesystem)
    {
        $this->runner = $runner;
        $this->git = $git;
        $this->filesystem = $filesystem;

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
        $commitMessage = $this->filesystem->exists($this->input->getArgument('commit-message-file'))
            ? $this->filesystem->readFromFileInfo(new SplFileInfo($this->input->getArgument('commit-message-file')))
            : $this->git->getCommitMessage();

        return new CommitMsgContext(
            $this->runner,
            $this->git->getChangedFiles($this->consoleIO()->readCommandInput(STDIN)),
            $commitMessage,
            $this->input->getOption('git-user-name') ?: $this->git->getUserName(),
            $this->input->getOption('git-user-email') ?: $this->git->getUserEmail()
        );
    }
}
