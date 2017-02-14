<?php

namespace ClickNow\Checker\IO;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;

interface IOInterface extends OutputInterface, StyleInterface
{
    /**
     * Is interactive?
     *
     * @return bool
     */
    public function isInteractive();

    /**
     * Create progress bar.
     *
     * @param int $max
     *
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function createProgressBar($max = 0);

    /**
     * Log.
     *
     * @param string $message
     *
     * @return void
     */
    public function log($message);

    /**
     * Success text.
     *
     * @param string $message
     *
     * @return void
     */
    public function successText($message);

    /**
     * Warning text.
     *
     * @param string $message
     *
     * @return void
     */
    public function warningText($message);

    /**
     * Error text.
     *
     * @param string $message
     *
     * @return void
     */
    public function errorText($message);
}
