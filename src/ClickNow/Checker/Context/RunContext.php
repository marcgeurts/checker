<?php

namespace ClickNow\Checker\Context;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Util\FilesCollection;

class RunContext implements ContextInterface
{
    /**
     * @var \ClickNow\Checker\Command\CommandInterface
     */
    private $command;

    /**
     * @var \ClickNow\Checker\Util\FilesCollection
     */
    private $files;

    /**
     * Run context.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Util\FilesCollection     $files
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
     * Get files collection.
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function getFiles()
    {
        return $this->files;
    }
}
