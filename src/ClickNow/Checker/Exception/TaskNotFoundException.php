<?php

namespace ClickNow\Checker\Exception;

class TaskNotFoundException extends TaskException
{
    /**
     * Task not found exception.
     *
     * @param string      $taskName
     * @param null|string $message
     */
    public function __construct($taskName, $message = null)
    {
        if (!$message) {
            $message = sprintf('Task `%s` was not found.', $taskName);
        }

        parent::__construct($taskName, $message);
    }
}
