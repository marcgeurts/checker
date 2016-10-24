<?php

namespace ClickNow\Checker\Context;

interface ContextInterface
{
    /**
     * Get command.
     *
     * @return \ClickNow\Checker\Command\CommandInterface
     */
    public function getCommand();

    /**
     * Get files collection.
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function getFiles();
}
