<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\IO\ConsoleIO;
use ClickNow\Checker\Runner\ConfigRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRunnerCommand extends Command
{
    /**
     * @var array
     */
    private $options = [
        'process-timeout'         => 'Process timeout.',
        'process-async-wait'      => 'Process async wait.',
        'process-async-limit'     => 'Process async limit.',
        'stop-on-failure'         => 'Stop on failure.',
        'ignore-unstaged-changes' => 'Ignore unstaged changes.',
        'skip-success-output'     => 'Skip success output.',
    ];

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
        foreach ($this->options as $option => $description) {
            $this->addOption($option, null, InputOption::VALUE_REQUIRED, $description);
        }
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

        $context = $this->context();

        foreach (array_keys($this->options) as $option) {
            if ($this->input->getOption($option)) {
                $function = [$context->getRunner(), ConfigRunner::$configs[$option]];
                call_user_func($function, $this->input->getOption($option));
            }
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
