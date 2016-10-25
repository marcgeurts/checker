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
        $msg = $message;

        if ($msg === null) {
            $msg = sprintf('File `%s` was not found.', $path);
        }

        parent::__construct($msg);
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
