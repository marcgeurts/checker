<?php

namespace ClickNow\Checker\Exception;

class TaskException extends RuntimeException
{
    /**
     * @var string
     */
    private $taskName;

    /**
     * Task exception.
     *
     * @param string $taskName
     * @param string $message
     */
    public function __construct($taskName, $message)
    {
        $this->taskName = $taskName;

        parent::__construct($message);
    }

    /**
     * Get task name.
     *
     * @return string
     */
    public function getTaskName()
    {
        return $this->taskName;
    }
}
