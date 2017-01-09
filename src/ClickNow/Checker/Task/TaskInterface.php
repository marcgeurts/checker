<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Action\ActionInterface;

interface TaskInterface extends ActionInterface
{
    /**
     * Merge default config.
     *
     * @param array $config
     *
     * @return void
     */
    public function mergeDefaultConfig(array $config);
}
