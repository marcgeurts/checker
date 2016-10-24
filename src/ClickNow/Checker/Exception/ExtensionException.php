<?php

namespace ClickNow\Checker\Exception;

class ExtensionException extends RuntimeException
{
    /**
     * @var string
     */
    private $extensionClass;

    /**
     * Extension exception.
     *
     * @param string $extensionClass
     * @param string $message
     */
    public function __construct($extensionClass, $message)
    {
        $this->extensionClass = $extensionClass;

        parent::__construct($message);
    }

    /**
     * Get extension class.
     *
     * @return string
     */
    public function getExtensionClass()
    {
        return $this->extensionClass;
    }
}
