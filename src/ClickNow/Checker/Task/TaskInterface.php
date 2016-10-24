<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Command\CommandInterface;

interface TaskInterface extends ActionInterface
{
    /**
     * Merge default config.
     *
     * @param array $config
     */
    public function mergeDefaultConfig(array $config);

    /**
     * Get config.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     *
     * @return array
     */
    public function getConfig(CommandInterface $command);

    /**
     * Get config options.
     *
     * @return \Symfony\Component\OptionsResolver\OptionsResolver
     */
    public function getConfigOptions();
}
