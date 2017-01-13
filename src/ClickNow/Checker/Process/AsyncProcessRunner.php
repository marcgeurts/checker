<?php

namespace ClickNow\Checker\Process;

use Symfony\Component\Process\Process;

class AsyncProcessRunner
{
    /**
     * @var array
     */
    private $processes;

    /**
     * @var int
     */
    private $running;

    /**
     * Run.
     *
     * @param array $processes
     *
     * @return void
     */
    public function run(array $processes)
    {
        $this->processes = $processes;
        $this->running = 0;
        $sleepDuration = $this->config->getProcessAsyncWaitTime();

        while ($this->watchProcesses()) {
            usleep($sleepDuration);
        }
    }

    /**
     * Watch processes.
     *
     * @return bool
     */
    private function watchProcesses()
    {
        foreach ($this->processes as $key => $process) {
            $isTerminated = $this->handleProcess($process);

            if ($isTerminated) {
                unset($this->processes[$key]);
            }
        }

        return count($this->processes) !== 0;
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

        if ($this->running < $this->config->getProcessAsyncLimit()) {
            $process->start();
            $this->running++;
        }

        return false;
    }
}
