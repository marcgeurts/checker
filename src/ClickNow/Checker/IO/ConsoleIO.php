<?php

namespace ClickNow\Checker\IO;

use ClickNow\Checker\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleIO extends SymfonyStyle implements IOInterface
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $stdin;

    /**
     * Console the input and output.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);

        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Is this input means interactive?
     *
     * @return bool
     */
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    /**
     * Log.
     *
     * @param string $message
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
     * @param resource $handle
     *
     * @throws \ClickNow\Checker\Exception\InvalidArgumentException
     *
     * @return string
     */
    public function readCommandInput($handle)
    {
        if (!is_resource($handle)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a resource stream for reading the commandline input. Got `%s`.',
                gettype($handle)
            ));
        }

        if ($this->stdin !== null || ftell($handle) !== 0) {
            return $this->stdin;
        }

        $input = '';
        while (!feof($handle)) {
            $input .= fread($handle, 1024);
        }

        // When the input only consist of white space characters, we assume that there is no input.
        $this->stdin = !preg_match('/^([\s]*)$/m', $input) ? $input : '';

        return $this->stdin;
    }
}
