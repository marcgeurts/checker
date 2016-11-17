<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Command\CommandsCollection;
use ClickNow\Checker\Context\RunContext;
use ClickNow\Checker\Exception\CommandInvalidException;
use ClickNow\Checker\Exception\CommandNotFoundException;
use ClickNow\Checker\Repository\Git;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends SymfonyCommand
{
    /**
     * @var \ClickNow\Checker\Command\CommandsCollection
     */
    private $commandsCollection;

    /**
     * @var \ClickNow\Checker\Repository\Git
     */
    private $git;

    /**
     * Hook command.
     *
     * @param \ClickNow\Checker\Command\CommandsCollection $commandsCollection
     * @param \ClickNow\Checker\Repository\Git             $git
     */
    public function __construct(CommandsCollection $commandsCollection, Git $git)
    {
        $this->commandsCollection = $commandsCollection;
        $this->git = $git;

        parent::__construct();
    }

    /**
     * Configure.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run specified command name')
            ->addArgument('name', InputArgument::REQUIRED, 'The command name to be executed')
            ->addOption('process-timeout', null, InputOption::VALUE_REQUIRED, 'Process timeout.')
            ->addOption('stop-on-failure', null, InputOption::VALUE_REQUIRED, 'Stop on failure.')
            ->addOption('ignore-unstaged-changes', null, InputOption::VALUE_REQUIRED, 'Ignore unstaged changes.')
            ->addOption('skip-success-output', null, InputOption::VALUE_REQUIRED, 'Skip success output.');
    }

    /**
     * Execute.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getCommand($input->getArgument('name'));
        $command->setConfig($this->parseConfig($input));

        $files = $this->git->getRegisteredFiles();
        $context = new RunContext($command, $files);

        return $this->runner()->run($context);
    }

    /**
     * Get command by name.
     *
     * @param string $name
     *
     * @throws \ClickNow\Checker\Exception\CommandNotFoundException
     * @throws \ClickNow\Checker\Exception\CommandInvalidException
     *
     * @return \ClickNow\Checker\Command\CommandInterface
     */
    private function getCommand($name)
    {
        if (!$this->commandsCollection->containsKey($name)) {
            throw new CommandNotFoundException($name);
        }

        $command = $this->commandsCollection->get($name);

        if (!$command instanceof CommandInterface) {
            throw new CommandInvalidException($name);
        }

        return $command;
    }

    /**
     * Parse config.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return array
     */
    private function parseConfig(InputInterface $input)
    {
        $config = [];

        if (!is_null($input->getOption('process-timeout'))) {
            $config['process_timeout'] = (float) $input->getOption('process-timeout');
        }

        $options = ['stop-on-failure', 'ignore-unstaged-changes', 'skip-success-output'];
        foreach ($options as $option) {
            if (!is_null($input->getOption($option))) {
                $config[str_replace('-', '_', $option)] = (bool) $input->getOption($option);
            }
        }

        return $config;
    }

    /**
     * Runner helper.
     *
     * @return \ClickNow\Checker\Console\Helper\RunnerHelper
     */
    private function runner()
    {
        return $this->getHelperSet()->get('runner');
    }
}
