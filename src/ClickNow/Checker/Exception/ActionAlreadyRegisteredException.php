<?php

namespace ClickNow\Checker\Exception;

class ActionAlreadyRegisteredException extends ActionException
{
    /**
     * Action already registered exception.
     *
     * @param string      $actionName
     * @param null|string $message
     */
    public function __construct($actionName, $message = null)
    {
        if ($message === null) {
            $message = sprintf('Action `%s` already registered.', $actionName);
        }

        parent::__construct($actionName, $message);
    }
}
