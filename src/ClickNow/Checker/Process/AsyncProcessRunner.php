<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Command\CommandInterface;
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
     * @var \ClickNow\Checker\Command\CommandInterface
     */
    private $command;

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
     * @param \ClickNow\Checker\Command\CommandInterface $command
     *
     * @return void
     */
    public function run(CommandInterface $command)
    {
        $this->running = 0;
        $this->command = $command;

        while ($this->watchProcesses()) {
            usleep($this->command->getProcessAsyncWait());
        }
    }

    /**
     * Watch processes.
     *
     * @return bool
     */
    private function watchProcesses()
    {
        $this->processCollection = $this->processCollection->filter(function (Process $process) {
            return !$this->handleProcess($process);
        });

        return !$this->processCollection->isEmpty();
    }

    /**
     * Handle process.
     *
     * @param \Symfony\Component\Process\Process $process
     *
     * @return bool
     */
    private function handleProcess(Process $process)
    {
        if ($process->isStarted()) {
            if ($process->isTerminated()) {
                $this->running--;
                return true;
            }

            return false;
        }

        if ($this->running < $this->command->getProcessAsyncLimit()) {
            $process->start();
            $this->running++;
        }

        return false;
    }
}
