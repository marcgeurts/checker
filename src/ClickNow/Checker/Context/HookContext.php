<?php

namespace ClickNow\Checker\Context;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Repository\FilesCollection;

class HookContext implements ContextInterface
{
    /**
     * @var \ClickNow\Checker\Command\CommandInterface
     */
    private $command;

    /**
     * @var \ClickNow\Checker\Repository\FilesCollection
     */
    private $files;

    /**
     * Hook context.
     *
     * @param \ClickNow\Checker\Command\CommandInterface   $command
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     */
    public function __construct(CommandInterface $command, FilesCollection $files)
    {
        $this->command = $command;
        $this->files = $files;
    }

    /**
     * Get command.
     *
     * @return \ClickNow\Checker\Command\CommandInterface
     */
    public function getCommand()
    {
        return $this->command;
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
