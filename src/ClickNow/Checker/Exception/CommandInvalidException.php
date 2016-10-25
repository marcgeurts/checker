<?php

namespace ClickNow\Checker\Exception;

class CommandInvalidException extends CommandException
{
    /**
     * Command invalid exception.
     *
     * @param string      $commandName
     * @param null|string $message
     */
    public function __construct($commandName, $message = null)
    {
        if ($message === null) {
            $message = sprintf('Command `%s` must implement CommandInterface.', $commandName);
        }

        parent::__construct($commandName, $message);
    }
}
