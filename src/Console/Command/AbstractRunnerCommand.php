<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\IO\ConsoleIO;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractRunnerCommand extends Command
{
    /**
     * @var array
     */
    private $optionsConfig = [
        'process-timeout'              => ['setProcessTimeout', null, InputOption::VALUE_REQUIRED],
        'process-async-wait'           => ['setProcessAsyncWait', null, InputOption::VALUE_REQUIRED],
        'process-async-limit'          => ['setProcessAsyncLimit', null, InputOption::VALUE_REQUIRED],
        'stop-on-failure'              => ['setStopOnFailure', true, InputOption::VALUE_NONE],
        'no-stop-on-failure'           => ['setStopOnFailure', false, InputOption::VALUE_NONE],
        'ignore-unstaged-changes'      => ['setIgnoreUnstagedChanges', true, InputOption::VALUE_NONE],
        'no-ignore-unstaged-changes'   => ['setIgnoreUnstagedChanges', false, InputOption::VALUE_NONE],
        'strict'                       => ['setStrict', true, InputOption::VALUE_NONE],
        'no-strict'                    => ['setStrict', false, InputOption::VALUE_NONE],
        'progress'                     => ['setProgress', null, InputOption::VALUE_REQUIRED],
        'no-progress'                  => ['setProgress', null, InputOption::VALUE_NONE],
        'skip-empty-output'            => ['setSkipEmptyOutput', true, InputOption::VALUE_NONE],
        'no-skip-empty-output'         => ['setSkipEmptyOutput', false, InputOption::VALUE_NONE],
        'skip-success-output'          => ['setSkipSuccessOutput', true, InputOption::VALUE_NONE],
        'no-skip-success-output'       => ['setSkipSuccessOutput', false, InputOption::VALUE_NONE],
        'skip-circumvention-output'    => ['setSkipCircumventionOutput', true, InputOption::VALUE_NONE],
        'no-skip-circumvention-output' => ['setSkipCircumventionOutput', false, InputOption::VALUE_NONE],
    ];

    /**
     * @var array
     */
    private $optionsDescription = [
        'process-timeout'              => 'Specify process timeout.',
        'process-async-wait'           => 'Specify process async wait.',
        'process-async-limit'          => 'Specify process async limit.',
        'stop-on-failure'              => 'Stop on failure.',
        'no-stop-on-failure'           => 'Non stop on failure.',
        'ignore-unstaged-changes'      => 'Ignore unstaged changes.',
        'no-ignore-unstaged-changes'   => 'No ignore unstaged changes.',
        'strict'                       => 'Enable strict mode.',
        'no-strict'                    => 'Disable strict mode.',
        'progress'                     => 'Specify process style.',
        'no-progress'                  => 'Disable process style.',
        'skip-empty-output'            => 'Skip empty output.',
        'no-skip-empty-output'         => 'No skip empty output.',
        'skip-success-output'          => 'Skip success output.',
        'no-skip-success-output'       => 'No skip success output.',
        'skip-circumvention-output'    => 'Skip circumvention output.',
        'no-skip-circumvention-output' => 'No skip circumvention output.',
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
        foreach ($this->optionsConfig as $key => $values) {
            $this->addOption($key, null, $values[2], $this->optionsDescription[$key]);
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
        $this->setOptions($context->getRunner());

        return $this->getRunnerHelper()->run($context);
    }

    /**
     * Set options.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return void
     */
    private function setOptions(RunnerInterface $runner)
    {
        $options = $this->input->getOptions();

        foreach ($options as $key => $value) {
            if (!array_key_exists($key, $this->optionsConfig) || !$value) {
                continue;
            }

            $this->setOption($runner, $this->optionsConfig[$key], $value);
        }
    }

    /**
     * Set option.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     * @param array                                    $config
     * @param mixed                                    $value
     *
     * @return void
     */
    private function setOption(RunnerInterface $runner, array $config, $value)
    {
        call_user_func_array([$runner, $config[0]], [($config[2] == InputOption::VALUE_NONE) ? $config[1] : $value]);
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
     * Get runner helper.
     *
     * @return \ClickNow\Checker\Helper\RunnerHelper
     */
    protected function getRunnerHelper()
    {
        return $this->getHelperSet()->get('runner');
    }
}
