<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Exception\PlatformException;
use Symfony\Component\Process\ProcessBuilder as SymfonyProcessBuilder;

class Platform
{
    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     */
    private $processBuilder;

    /**
     * Platform.
     *
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder
     */
    public function __construct(SymfonyProcessBuilder $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * Validate command line max length.
     *
     * @param string $commandLine
     *
     * @throws \ClickNow\Checker\Exception\PlatformException
     *
     * @return void
     */
    public function validateCommandLineMaxLength($commandLine)
    {
        $maxLength = $this->getCommandLineMaxLength();
        if (strlen($commandLine) <= $maxLength) {
            return;
        }

        throw new PlatformException(sprintf(
            'The maximum amount of `%s` input characters exceeded while running process: %s ...',
            $maxLength,
            substr($commandLine, 0, 75)
        ));
    }

    /**
     * Get command line max length.
     *
     * @return int
     */
    private function getCommandLineMaxLength()
    {
        $builder = $this->processBuilder->setArguments(['getconf', 'ARG_MAX']);
        $process = $builder->getProcess();
        $process->enableOutput();
        $process->run();

        if (!$process->isSuccessful()) {
            /**
             * Windows has a limit on command line input strings.
             * This one is causing external commands to fail with exit code 1 without any error.
             * More information:
             *
             * @link https://support.microsoft.com/en-us/kb/830473
             */
            return 8191;
        }

        return (int) $process->getOutput();
    }
}
