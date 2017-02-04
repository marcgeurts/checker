<?php

namespace ClickNow\Checker\Context;

interface ContextInterface
{
    /**
     * Get runner.
     *
     * @return \ClickNow\Checker\Runner\RunnerInterface
     */
    public function getRunner();

    /**
     * Get files.
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function getFiles();
}
