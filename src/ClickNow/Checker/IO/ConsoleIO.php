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
    private $consoleInput;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $consoleOutput;

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
     * @return string
     */
    public function readCommandInput($handle)
    {
        $resource = $this->validateResource($handle);

        if ($this->stdin === null && ftell($resource) === 0) {
            $this->stdin = $this->prepareCommandInput($resource);
        }

        return $this->stdin;
    }

    /**
     * Validate resource.
     *
     * @param mixed $handle
     *
     * @throws \ClickNow\Checker\Exception\InvalidArgumentException
     *
     * @return resource
     */
    private function validateResource($handle)
    {
        if (!is_resource($handle)) {
            throw new InvalidArgumentException(sprintf(
                'Expected a resource stream for reading the commandline input. Got `%s`.',
                gettype($handle)
            ));
        }

        return $handle;
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

        // When the input only consist of white space characters, we assume that there is no input.
        return !preg_match('/^([\s]*)$/m', $input) ? $input : '';
    }
}
