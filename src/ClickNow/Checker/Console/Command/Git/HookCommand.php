<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\HookContext;
use ClickNow\Checker\IO\ConsoleIO;
use ClickNow\Checker\Util\Git;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HookCommand extends SymfonyCommand
{
    /**
     * @var \ClickNow\Checker\Command\CommandInterface
     */
    private $command;

    /**
     * @var \ClickNow\Checker\Util\Git
     */
    private $git;

    /**
     * Hook command.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Util\Git                 $git
     */
    public function __construct(CommandInterface $command, Git $git)
    {
        $this->command = $command;
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
            ->setName(sprintf('git:%s', $this->command->getName()))
            ->setDescription(sprintf('Git hook %s', $this->command->getName()))
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
        $this->command->setConfig($this->parseConfig($input));

        $io = new ConsoleIO($input, $output);
        $stdin = $io->readCommandInput(STDIN);

        $files = $this->git->getChangedFiles($stdin);
        $context = new HookContext($this->command, $files);

        return $this->runner()->run($context);
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

        if (!is_null($input->getOption('stop-on-failure'))) {
            $config['stop_on_failure'] = (bool) $input->getOption('stop-on-failure');
        }

        if (!is_null($input->getOption('ignore-unstaged-changes'))) {
            $config['ignore_unstaged_changes'] = (bool) $input->getOption('ignore-unstaged-changes');
        }

        if (!is_null($input->getOption('skip-success-output'))) {
            $config['skip_success_output'] = (bool) $input->getOption('skip-success-output');
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
