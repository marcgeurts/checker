<?php

namespace ClickNow\Checker\Exception;

class ActionNotFoundException extends ActionException
{
    /**
     * Action not found exception.
     *
     * @param string      $actionName
     * @param null|string $message
     */
    public function __construct($actionName, $message = null)
    {
        $msg = $message;

        if ($msg === null) {
            $msg = sprintf('Action `%s` was not found.', $actionName);
        }

        parent::__construct($actionName, $msg);
    }
}
