<?php

namespace ClickNow\Checker\IO;

use Symfony\Component\Console\Output\NullOutput;

class NullIO extends NullOutput implements IOInterface
{
    /**
     * {@inheritdoc}
     */
    public function isInteractive()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createProgressBar($max = 0)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function successText($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function warningText($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function errorText($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function title($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function section($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function listing(array $elements)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function text($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function success($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function error($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function note($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function caution($message)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function table(array $headers, array $rows)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null, $validator = null)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function askHidden($question, $validator = null)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function confirm($question, $default = true)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function choice($question, array $choices, $default = null)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function newLine($count = 1)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function progressStart($max = 0)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function progressAdvance($step = 1)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function progressFinish()
    {
        // do nothing
    }
}
