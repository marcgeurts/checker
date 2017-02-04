<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Runner\RunnerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Process\Process;

class AsyncProcessRunner
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $processCollection;

    /**
     * @var int
     */
    private $running;

    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface
     */
    private $runner;

    /**
     * Async process runner.
     */
    public function __construct()
    {
        $this->processCollection = new ArrayCollection();
    }

    /**
     * Add.
     *
     * @param \Symfony\Component\Process\Process $process
     */
    public function add(Process $process)
    {
        $this->processCollection->add($process);
    }

    /**
     * Run.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return void
     */
    public function run(RunnerInterface $runner)
    {
        $this->running = 0;
        $this->runner = $runner;

        while ($this->hasProcesses()) {
            usleep($this->runner->getProcessAsyncWait());
        }
    }

    /**
     * Has processes?
     *
     * @return bool
     */
    private function hasProcesses()
    {
        $this->processCollection = $this->processCollection->filter(function (Process $process) {
            return $this->isPendingProcess($process);
        });

        return !$this->processCollection->isEmpty();
    }

    /**
     * Is pending process?
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return bool
     */
    private function isPendingProcess(Process $process)
    {
        if ($process->isStarted()) {
            if ($process->isTerminated()) {
                $this->running--;

                return false;
            }

            return true;
        }

        if ($this->running < $this->runner->getProcessAsyncLimit()) {
            $process->start();
            $this->running++;
        }

        return true;
    }
}
