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
            ->addOption('process-timeout', null, InputOption::VALUE_REQUIRED, 'Process timeout.')
            ->addOption('process-async-wait', null, InputOption::VALUE_REQUIRED, 'Process async wait.')
            ->addOption('process-async-limit', null, InputOption::VALUE_REQUIRED, 'Process async limit.')
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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $config = [];
        /*if($this->input->hasOption('process-timeout')) {
            $config['process-timeout'] = (bool) $this->input->getOption('process-timeout');
        }*/

        $context = $this->context();
        $context->getRunner()->setConfig($config);

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
     * Paths helper.
     *
     * @return \ClickNow\Checker\Helper\PathsHelper
     */
    protected function paths()
    {
        return $this->getHelperSet()->get('paths');
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
