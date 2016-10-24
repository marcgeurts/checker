<?php

namespace ClickNow\Checker\Formatter;

use Symfony\Component\Process\Process;

interface ProcessFormatterInterface
{
    /**
     * Format.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return string
     */
    public function format(Process $process);
}
