<?php

namespace ClickNow\Checker\Exception;

class TaskAlreadyRegisteredException extends TaskException
{
    /**
     * Task already registered exception.
     *
     * @param string      $taskName
     * @param null|string $message
     */
    public function __construct($taskName, $message = null)
    {
        if (! $message) {
            $message = sprintf('Task `%s` already registered.', $taskName);
        }

        parent::__construct($taskName, $message);
    }
}
