<?php

namespace ClickNow\Checker\Exception;

class FileNotFoundException extends RuntimeException
{
    /**
     * @var string
     */
    private $path;

    /**
     * File not found exception.
     *
     * @param string      $path
     * @param null|string $message
     */
    public function __construct($path, $message = null)
    {
        $this->path = $path;

        if (! $message) {
            $message = sprintf('File `%s` was not found.', $path);
        }

        parent::__construct($message);
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
