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
        parent::__construct($input, $output);

        $this->consoleInput = $input;
        $this->consoleOutput = $output;
    }

    /**
     * Is this input means interactive?
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
        if (!$this->isVeryVerbose() || !$message) {
            return;
        }

        $this->newLine();
        $this->text($message);
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

        $input = '';
        while (!feof($handle)) {
            $input .= fread($handle, 1024);
        }

        return !preg_match('/^([\s]*)$/m', $input) ? $input : '';
    }
}
