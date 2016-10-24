<?php

namespace ClickNow\Checker\Exception;

class CommandException extends RuntimeException
{
    /**
     * @var string
     */
    private $commandName;

    /**
     * Command exception.
     *
     * @param string $commandName
     * @param string $message
     */
    public function __construct($commandName, $message)
    {
        $this->commandName = $commandName;

        parent::__construct($message);
    }

    /**
     * Get command name.
     *
     * @return string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }
}
