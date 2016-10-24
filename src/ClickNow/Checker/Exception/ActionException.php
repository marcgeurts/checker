<?php

namespace ClickNow\Checker\Exception;

class ActionException extends RuntimeException
{
    /**
     * @var string
     */
    private $actionName;

    /**
     * Action exception.
     *
     * @param string $actionName
     * @param string $message
     */
    public function __construct($actionName, $message)
    {
        $this->actionName = $actionName;

        parent::__construct($message);
    }

    /**
     * Get action name.
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }
}
