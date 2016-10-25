<?php

namespace ClickNow\Checker\Exception;

class ExtensionInvalidException extends ExtensionException
{
    /**
     * Extension invalid exception.
     *
     * @param string      $extensionClass
     * @param null|string $message
     */
    public function __construct($extensionClass, $message = null)
    {
        $msg = $message;

        if ($msg === null) {
            $msg = sprintf('Extension `%s` must implement ExtensionInterface.', $extensionClass);
        }

        parent::__construct($extensionClass, $msg);
    }
}
