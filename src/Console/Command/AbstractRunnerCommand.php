<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\IO\ConsoleIO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRunnerCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Configure.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->addOption('process-timeout', null, InputOption::VALUE_REQUIRED, 'Specify process timeout.')
            ->addOption('process-async-wait', null, InputOption::VALUE_REQUIRED, 'Specify process async wait.')
            ->addOption('process-async-limit', null, InputOption::VALUE_REQUIRED, 'Specify process async limit.')
            ->addOption('stop-on-failure', null, InputOption::VALUE_NONE, 'Stop on failure.')
            ->addOption('no-stop-on-failure', null, InputOption::VALUE_NONE, 'Non stop on failure.')
            ->addOption('ignore-unstaged-changes', null, InputOption::VALUE_NONE, 'Ignore unstaged changes.')
            ->addOption('no-ignore-unstaged-changes', null, InputOption::VALUE_NONE, 'No ignore unstaged changes.')
            ->addOption('strict', null, InputOption::VALUE_NONE, 'Enable strict mode.')
            ->addOption('no-strict', null, InputOption::VALUE_NONE, 'Disable strict mode.')
            ->addOption('progress', null, InputOption::VALUE_REQUIRED, 'Specify process style.')
            ->addOption('no-progress', null, InputOption::VALUE_NONE, 'Disable process style.')
            ->addOption('skip-success-output', null, InputOption::VALUE_NONE, 'Skip success output.')
            ->addOption('no-skip-success-output', null, InputOption::VALUE_NONE, 'No skip success output.');
    }

    /**
     * Execute.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $context = $this->context();
        $runner = $context->getRunner();

        if ($this->input->getOption('process-timeout')) {
            $runner->setProcessTimeout($this->input->getOption('process-timeout'));
        }

        if ($this->input->getOption('process-async-wait')) {
            $runner->setProcessAsyncWait($this->input->getOption('process-async-wait'));
        }

        if ($this->input->getOption('process-async-limit')) {
            $runner->setProcessAsyncLimit($this->input->getOption('process-async-limit'));
        }

        if ($this->input->getOption('stop-on-failure')) {
            $runner->setStopOnFailure(true);
        }

        if ($this->input->getOption('no-stop-on-failure')) {
            $runner->setStopOnFailure(false);
        }

        if ($this->input->getOption('ignore-unstaged-changes')) {
            $runner->setIgnoreUnstagedChanges(true);
        }

        if ($this->input->getOption('no-ignore-unstaged-changes')) {
            $runner->setIgnoreUnstagedChanges(false);
        }

        if ($this->input->getOption('strict')) {
            $runner->setStrict(true);
        }

        if ($this->input->getOption('no-strict')) {
            $runner->setStrict(false);
        }

        if ($this->input->getOption('progress')) {
            $runner->setProgress($this->input->getOption('progress'));
        }

        if ($this->input->getOption('no-progress')) {
            $runner->setProgress(null);
        }

        if ($this->input->getOption('skip-success-output')) {
            $runner->setSkipSuccessOutput(true);
        }

        if ($this->input->getOption('no-skip-success-output')) {
            $runner->setSkipSuccessOutput(false);
        }

        return $this->runner()->run($context);
    }

    /**
     * Context.
     *
     * @return \ClickNow\Checker\Context\ContextInterface
     */
    abstract protected function context();

    /**
     * Console IO.
     *
     * @return \ClickNow\Checker\IO\ConsoleIO
     */
    protected function consoleIO()
    {
        return new ConsoleIO($this->input, $this->output);
    }

    /**
     * Runner helper.
     *
     * @return \ClickNow\Checker\Helper\RunnerHelper
     */
    protected function runner()
    {
        return $this->getHelperSet()->get('runner');
    }
}
