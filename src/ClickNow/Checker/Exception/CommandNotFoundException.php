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
        $msg = $message;

        if ($msg === null) {
            $msg = sprintf('Command `%s` was not found.', $commandName);
        }

        parent::__construct($commandName, $msg);
    }
}
