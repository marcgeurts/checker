<?php

namespace ClickNow\Checker\Exception;

class ActionInvalidResultException extends ActionException
{
    /**
     * Action invalid result exception.
     *
     * @param string      $actionName
     * @param null|string $message
     */
    public function __construct($actionName, $message = null)
    {
        $msg = $message;

        if ($msg === null) {
            $msg = sprintf('Action `%s` did not return a Result.', $actionName);
        }

        parent::__construct($actionName, $msg);
    }
}
