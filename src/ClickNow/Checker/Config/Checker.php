<?php

namespace ClickNow\Checker\Config;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Checker
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * Checker.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get bin directory.
     *
     * @return string
     */
    public function getBinDir()
    {
        return (string) $this->container->getParameter('bin_dir');
    }

    /**
     * Get git directory.
     *
     * @return string
     */
    public function getGitDir()
    {
        return (string) $this->container->getParameter('git_dir');
    }

    /**
     * Get hooks directory.
     *
     * @return string
     */
    public function getHooksDir()
    {
        return (string) $this->container->getParameter('hooks_dir');
    }

    /**
     * Get hooks preset.
     *
     * @return string
     */
    public function getHooksPreset()
    {
        return (string) $this->container->getParameter('hooks_preset');
    }

    /**
     * Get process timeout.
     *
     * @return null|float
     */
    public function getProcessTimeout()
    {
        $timeout = $this->container->getParameter('process_timeout');

        if (is_null($timeout)) {
            return null;
        }

        return (float) $timeout;
    }

    /**
     * Get process async wait.
     *
     * @return int
     */
    public function getProcessAsyncWait()
    {
        return (int) $this->container->getParameter('process_async_wait');
    }

    /**
     * Get process async limit.
     *
     * @return int
     */
    public function getProcessAsyncLimit()
    {
        return (int) $this->container->getParameter('process_async_limit');
    }

    /**
     * Is stop on failure?
     *
     * @return bool
     */
    public function isStopOnFailure()
    {
        return (bool) $this->container->getParameter('stop_on_failure');
    }

    /**
     * Is ignore unstaged changes?
     *
     * @return bool
     */
    public function isIgnoreUnstagedChanges()
    {
        return (bool) $this->container->getParameter('ignore_unstaged_changes');
    }

    /**
     * It is to skip the success output?
     *
     * @return bool
     */
    public function isSkipSuccessOutput()
    {
        return (bool) $this->container->getParameter('skip_success_output');
    }

    /**
     * Get message by resource.
     *
     * @param string $resource
     *
     * @return null|string
     */
    public function getMessage($resource)
    {
        $message = $this->container->getParameter('message');

        if (!is_array($message) || !array_key_exists($resource, $message)) {
            return null;
        }

        return (string) $message[$resource];
    }
}
