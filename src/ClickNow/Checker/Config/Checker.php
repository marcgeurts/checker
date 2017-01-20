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
     * Get bin dir.
     *
     * @return string
     */
    public function getBinDir()
    {
        return (string) $this->container->getParameter('bin-dir');
    }

    /**
     * Get git dir.
     *
     * @return string
     */
    public function getGitDir()
    {
        return (string) $this->container->getParameter('git-dir');
    }

    /**
     * Get hooks dir.
     *
     * @return string
     */
    public function getHooksDir()
    {
        return (string) $this->container->getParameter('hooks-dir');
    }

    /**
     * Get hooks preset.
     *
     * @return string
     */
    public function getHooksPreset()
    {
        return (string) $this->container->getParameter('hooks-preset');
    }

    /**
     * Get process timeout.
     *
     * @return null|float
     */
    public function getProcessTimeout()
    {
        $timeout = $this->container->getParameter('process-timeout');

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
        return (int) $this->container->getParameter('process-async-wait');
    }

    /**
     * Get process async limit.
     *
     * @return int
     */
    public function getProcessAsyncLimit()
    {
        return (int) $this->container->getParameter('process-async-limit');
    }

    /**
     * Is stop on failure?
     *
     * @return bool
     */
    public function isStopOnFailure()
    {
        return (bool) $this->container->getParameter('stop-on-failure');
    }

    /**
     * Is ignore unstaged changes?
     *
     * @return bool
     */
    public function isIgnoreUnstagedChanges()
    {
        return (bool) $this->container->getParameter('ignore-unstaged-changes');
    }

    /**
     * Is skip success output?
     *
     * @return bool
     */
    public function isSkipSuccessOutput()
    {
        return (bool) $this->container->getParameter('skip-success-output');
    }

    /**
     * Get message.
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
