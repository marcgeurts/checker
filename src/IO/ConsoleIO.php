<?php

namespace ClickNow\Checker\IO;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleIO extends SymfonyStyle implements IOInterface
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $consoleInput;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $consoleOutput;

    /**
     * Console the input and output.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->consoleInput = $input;
        $this->consoleOutput = $output;

        parent::__construct($this->consoleInput, $this->consoleOutput);
    }

    /**
     * Is interactive?
     *
     * @return bool
     */
    public function isInteractive()
    {
        return $this->consoleInput->isInteractive();
    }

    /**
     * Log.
     *
     * @param string $message
     *
     * @return void
     */
    public function log($message)
    {
        if (!$this->isVerbose() || !$message) {
            return;
        }

        $this->newLine();
        $this->text($message);
    }

    /**
     * Warning.
     *
     * @param string|array $message
     *
     * @return void
     */
    public function warning($message)
    {
        $this->block($message, 'WARNING', 'fg=white;bg=yellow', ' ', true);
    }

    /**
     * Success text.
     *
     * @param string $message
     *
     * @return void
     */
    public function successText($message)
    {
        $this->text(sprintf('<fg=green>%s</fg=green>', $message));
    }

    /**
     * Warning text.
     *
     * @param string $message
     *
     * @return void
     */
    public function warningText($message)
    {
        $this->text(sprintf('<fg=yellow>%s</fg=yellow>', $message));
    }

    /**
     * Error text.
     *
     * @param string $message
     *
     * @return void
     */
    public function errorText($message)
    {
        $this->text(sprintf('<fg=red>%s</fg=red>', $message));
    }

    /**
     * Read command input.
     *
     * @param mixed $handle
     *
     * @return null|string
     */
    public function readCommandInput($handle)
    {
        if (!is_resource($handle) || ftell($handle) !== 0) {
            return null;
        }

        return $this->prepareCommandInput($handle);
    }

    /**
     * Prepare command input.
     *
     * @param resource $handle
     *
     * @return string
     */
    private function prepareCommandInput($handle)
    {
        $input = '';
        while (!feof($handle)) {
            $input .= fread($handle, 1024);
        }

        return !preg_match_all('/^([\s]*)$/', $input) ? $input : '';
    }
}
