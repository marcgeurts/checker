<?php

namespace ClickNow\Checker\Exception;

class CommandAlreadyRegisteredException extends CommandException
{
    /**
     * Command already registered exception.
     *
     * @param string      $commandName
     * @param null|string $message
     */
    public function __construct($commandName, $message = null)
    {
        if ($message === null) {
            $message = sprintf('Command `%s` already registered.', $commandName);
        }

        parent::__construct($commandName, $message);
    }
}
