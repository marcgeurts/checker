<?php

namespace ClickNow\Checker\Process;

use ClickNow\Checker\Exception\InvalidArgumentException;
use ClickNow\Checker\Repository\FilesCollection;
use Doctrine\Common\Collections\ArrayCollection;

class ArgumentsCollection extends ArrayCollection
{
    /**
     * For executable.
     *
     * @param string $executable
     *
     * @return \ClickNow\Checker\Process\ArgumentsCollection
     */
    public static function forExecutable($executable)
    {
        return new self([$executable]);
    }

    /**
     * Add optional argument.
     *
     * @param string $argument
     * @param string $value
     *
     * @return void
     */
    public function addOptionalArgument($argument, $value)
    {
        if (!$value) {
            return;
        }

        $this->add(sprintf($argument, $value));
    }

    /**
     * Add optional argument with separated value.
     *
     * @param string $argument
     * @param string $value
     *
     * @return void
     */
    public function addOptionalArgumentWithSeparatedValue($argument, $value)
    {
        if (!$value) {
            return;
        }

        $this->add($argument);
        $this->add($value);
    }

    /**
     * Add optional comma separated argument.
     *
     * @param string $argument
     * @param array  $values
     * @param string $delimiter
     *
     * @return void
     */
    public function addOptionalCommaSeparatedArgument($argument, array $values, $delimiter = ',')
    {
        if (!count($values)) {
            return;
        }

        $this->add(sprintf($argument, implode($delimiter, $values)));
    }

    /**
     * Add optional comma separated argument with separated value.
     *
     * @param string $argument
     * @param array  $values
     * @param string $delimiter
     *
     * @return void
     */
    public function addOptionalCommaSeparatedArgumentWithSeparatedValue($argument, array $values, $delimiter = ',')
    {
        if (!count($values)) {
            return;
        }

        $this->add($argument);
        $this->add(implode($delimiter, $values));
    }

    /**
     * Add argument array.
     *
     * @param string $argument
     * @param array  $values
     *
     * @return void
     */
    public function addArgumentArray($argument, array $values)
    {
        foreach ($values as $value) {
            $this->add(sprintf($argument, $value));
        }
    }

    /**
     * Add argument array with separated value.
     *
     * @param string $argument
     * @param array  $values
     *
     * @return void
     */
    public function addArgumentArrayWithSeparatedValue($argument, array $values)
    {
        foreach ($values as $value) {
            $this->add(sprintf($argument, $value));
            $this->add($value);
        }
    }

    /**
     * Add separated argument array.
     *
     * @param string $argument
     * @param array  $values
     *
     * @return void
     */
    public function addSeparatedArgumentArray($argument, array $values)
    {
        if (!count($values)) {
            return;
        }

        $this->add($argument);

        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * Add required argument.
     *
     * @param string $argument
     * @param string $value
     *
     * @throws \ClickNow\Checker\Exception\InvalidArgumentException
     *
     * @return void
     */
    public function addRequiredArgument($argument, $value)
    {
        if (!$value) {
            throw new InvalidArgumentException(sprintf('The argument `%s` is required.', $argument));
        }

        $this->add(sprintf($argument, $value));
    }

    /**
     * Add files.
     *
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     *
     * @return void
     */
    public function addFiles(FilesCollection $files)
    {
        $this->addArgumentArray('%s', $files->getAllPaths());
    }

    /**
     * Add comma separated files.
     *
     * @param \ClickNow\Checker\Repository\FilesCollection $files
     *
     * @return void
     */
    public function addCommaSeparatedFiles(FilesCollection $files)
    {
        $this->addOptionalCommaSeparatedArgument('%s', $files->getAllPaths());
    }
}
