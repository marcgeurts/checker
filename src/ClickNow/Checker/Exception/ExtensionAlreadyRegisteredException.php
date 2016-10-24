<?php

namespace ClickNow\Checker\Exception;

class ExtensionAlreadyRegisteredException extends ExtensionException
{
    /**
     * Extension already registered exception.
     *
     * @param string      $extensionClass
     * @param null|string $message
     */
    public function __construct($extensionClass, $message = null)
    {
        if (! $message) {
            $message = sprintf('Extension `%s` already registered.', $extensionClass);
        }

        parent::__construct($extensionClass, $message);
    }
}
