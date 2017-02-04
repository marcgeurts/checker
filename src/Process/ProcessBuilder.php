<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\Process\ProcessBuilder as SymfonyProcessBuilder;

class ProcessBuilder
{
    /**
     * @var \ClickNow\Checker\Process\ExecutableFinder
     */
    private $executableFinder;

    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var \ClickNow\Checker\Process\Platform
     */
    private $platform;

    /**
     * Process builder.
     *
     * @param \ClickNow\Checker\Process\ExecutableFinder $executableFinder
     * @param \Symfony\Component\Process\ProcessBuilder  $processBuilder
     * @param \ClickNow\Checker\IO\IOInterface           $io
     * @param \ClickNow\Checker\Process\Platform         $platform
     */
    public function __construct(
        ExecutableFinder $executableFinder,
        SymfonyProcessBuilder $processBuilder,
        IOInterface $io,
        Platform $platform
    ) {
        $this->executableFinder = $executableFinder;
        $this->processBuilder = $processBuilder;
        $this->io = $io;
        $this->platform = $platform;
    }

    /**
     * Create arguments for command.
     *
     * @param string $command
     *
     * @return \ClickNow\Checker\Process\ArgumentsCollection
     */
    public function createArgumentsForCommand($command)
    {
        $executable = $this->executableFinder->find($command);

        return ArgumentsCollection::forExecutable($executable);
    }

    /**
     * Build process.
     *
     * @param \ClickNow\Checker\Process\ArgumentsCollection $arguments
     * @param \ClickNow\Checker\Runner\RunnerInterface      $runner
     *
     * @return \Symfony\Component\Process\Process
     */
    public function buildProcess(ArgumentsCollection $arguments, RunnerInterface $runner)
    {
        $builder = $this->processBuilder->setArguments($arguments->getValues());
        $builder->setTimeout($runner->getProcessTimeout());
        $process = $builder->getProcess();
        $commandLine = $process->getCommandLine();

        $this->io->log('Command: '.$commandLine);
        $this->platform->validateCommandLineMaxLength($commandLine);

        return $process;
    }
}
