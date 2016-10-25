<?php

namespace ClickNow\Checker\Exception;

class ExtensionNotFoundException extends ExtensionException
{
    /**
     * Extension not found exception.
     *
     * @param string      $extensionClass
     * @param null|string $message
     */
    public function __construct($extensionClass, $message = null)
    {
        $msg = $message;

        if ($msg === null) {
            $msg = sprintf('Extension `%s` was not found.', $extensionClass);
        }

        parent::__construct($extensionClass, $msg);
    }
}
