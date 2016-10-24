<?php

namespace ClickNow\Checker\Exception;

class DirectoryNotFoundException extends RuntimeException
{
    /**
     * @var string
     */
    private $directory;

    /**
     * Directory not found exception.
     *
     * @param string      $directory
     * @param null|string $message
     */
    public function __construct($directory, $message = null)
    {
        $this->directory = $directory;

        if (!$message) {
            $message = sprintf('Directory `%s` was not found.', $directory);
        }

        parent::__construct($message);
    }

    /**
     * Get directory.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
