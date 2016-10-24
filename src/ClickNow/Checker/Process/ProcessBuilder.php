<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\IO\IOInterface;
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
     * Process builder.
     *
     * @param \ClickNow\Checker\Process\ExecutableFinder $executableFinder
     * @param \Symfony\Component\Process\ProcessBuilder  $processBuilder
     * @param \ClickNow\Checker\IO\IOInterface           $io
     */
    public function __construct(
        ExecutableFinder $executableFinder,
        SymfonyProcessBuilder $processBuilder,
        IOInterface $io
    ) {
        $this->executableFinder = $executableFinder;
        $this->processBuilder = $processBuilder;
        $this->io = $io;
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
     * @param \ClickNow\Checker\Command\CommandInterface    $command
     *
     * @return \Symfony\Component\Process\Process
     */
    public function buildProcess(ArgumentsCollection $arguments, CommandInterface $command)
    {
        $builder = $this->processBuilder->setArguments($arguments->getValues());
        $builder->setTimeout($command->getProcessTimeout());
        $process = $builder->getProcess();
        $this->io->log('Command: '.$process->getCommandLine());

        return $process;
    }
}
