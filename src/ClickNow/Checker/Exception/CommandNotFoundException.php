<?php

namespace ClickNow\Checker\Exception;

class CommandNotFoundException extends CommandException
{
    /**
     * Command not found exception.
     *
     * @param string      $commandName
     * @param null|string $message
     */
    public function __construct($commandName, $message = null)
    {
        if ($message === null) {
            $message = sprintf('Command `%s` was not found.', $commandName);
        }

        parent::__construct($commandName, $message);
    }
}
