<?php

namespace ClickNow\Checker\Formatter;

use Symfony\Component\Process\Process;

class RawProcessFormatter implements ProcessFormatterInterface
{
    /**
     * This method will format the output of a Process object to a string.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return string
     */
    public function format(Process $process)
    {
        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();

        return trim($stdout.PHP_EOL.$stderr);
    }
}
