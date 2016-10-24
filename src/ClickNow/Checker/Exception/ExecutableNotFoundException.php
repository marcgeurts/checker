<?php

namespace ClickNow\Checker\Exception;

class ExecutableNotFoundException extends RuntimeException
{
    /**
     * @var string
     */
    private $executable;

    /**
     * Executable not found exception.
     *
     * @param string      $executable
     * @param null|string $message
     */
    public function __construct($executable, $message = null)
    {
        $this->executable = $executable;

        if (!$message) {
            $message = sprintf('Executable `%s` was not found.', $executable);
        }

        parent::__construct($message);
    }

    /**
     * Get executable.
     *
     * @return string
     */
    public function getExecutable()
    {
        return $this->executable;
    }
}
