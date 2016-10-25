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
        $msg = $message;

        if ($msg === null) {
            $msg = sprintf('Command `%s` must implement CommandInterface.', $commandName);
        }

        parent::__construct($commandName, $msg);
    }
}
