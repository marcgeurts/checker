<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Exception\ExecutableNotFoundException;
use Symfony\Component\Process\ExecutableFinder as SymfonyExecutableFinder;

class ExecutableFinder
{
    /**
     * @var string
     */
    protected $binDir;

    /**
     * @var \Symfony\Component\Process\ExecutableFinder
     */
    protected $executableFinder;

    /**
     * Executable finder.
     *
     * @param string                                      $binDir
     * @param \Symfony\Component\Process\ExecutableFinder $executableFinder
     */
    public function __construct($binDir, SymfonyExecutableFinder $executableFinder)
    {
        $this->binDir = rtrim($binDir, '/\\');
        $this->executableFinder = $executableFinder;
    }

    /**
     * Find.
     *
     * @param string $command
     * @param bool   $forceUnix
     *
     * @throws \ClickNow\Checker\Exception\ExecutableNotFoundException
     *
     * @return string
     */
    public function find($command, $forceUnix = false)
    {
        // Search executable
        $executable = $this->executableFinder->find($command, null, [$this->binDir]);
        if (!$executable) {
            throw new ExecutableNotFoundException($command);
        }

        // Make sure to add unix-style directory separators if unix-mode is enforced
        if ($forceUnix) {
            $parts = pathinfo($executable);
            $executable = $parts['dirname'].'/'.$parts['filename'];
        }

        return $executable;
    }
}
