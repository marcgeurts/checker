<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\Context\RunContext;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Repository\Git;
use ClickNow\Checker\Runner\CommandsCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\Console\Input\InputArgument;

class RunCommand extends AbstractRunnerCommand
{
    /**
     * @var \ClickNow\Checker\Runner\CommandsCollection
     */
    private $commandsCollection;

    /**
     * @var \ClickNow\Checker\Repository\Git
     */
    private $git;

    /**
     * Run command.
     *
     * @param \ClickNow\Checker\Runner\CommandsCollection $commandsCollection
     * @param \ClickNow\Checker\Repository\Git            $git
     */
    public function __construct(CommandsCollection $commandsCollection, Git $git)
    {
        $this->commandsCollection = $commandsCollection;
        $this->git = $git;

        parent::__construct('run');

        $this->setDescription('Run specified command name.');
        $this->addArgument('name', InputArgument::REQUIRED, 'The command name to be executed.');
    }

    /**
     * Context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    protected function context()
    {
        return new RunContext(
            $this->getRunner($this->input->getArgument('name')),
            $this->git->getRegisteredFiles()
        );
    }

    /**
     * Get runner.
     *
     * @param string $name
     *
     * @throws \ClickNow\Checker\Exception\CommandNotFoundException
     * @throws \ClickNow\Checker\Exception\CommandInvalidException
     *
     * @return \ClickNow\Checker\Runner\RunnerInterface
     */
    private function getRunner($name)
    {
        if (!$this->commandsCollection->containsKey($name)) {
            throw new CommandNotFoundException($name);
        }

        $runner = $this->commandsCollection->get($name);

        if (!$runner instanceof RunnerInterface) {
            throw new CommandInvalidException($name);
        }

        return $runner;
    }
}
