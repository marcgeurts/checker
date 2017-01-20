<?php

namespace ClickNow\Checker\Context\Git;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Repository\FilesCollection;
use ClickNow\Checker\Runner\RunnerInterface;

class CommitMsgContext implements ContextInterface
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
     * @var string
     */
    private $commitMessage;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $userEmail;

    /**
     * Git commit msg context.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface     $runner
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     * @param string                                       $commitMessage
     * @param string                                       $userName
     * @param string                                       $userEmail
     */
    public function __construct(RunnerInterface $runner, FilesCollection $files, $commitMessage, $userName, $userEmail)
    {
        $this->runner = $runner;
        $this->files = $files;
        $this->commitMessage = $commitMessage;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
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

    /**
     * Get commit message.
     *
     * @return string
     */
    public function getCommitMessage()
    {
        return $this->commitMessage;
    }

    /**
     * Get user name.
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Get user e-mail.
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }
}
