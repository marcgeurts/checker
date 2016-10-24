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
     * Stop on failure?
     *
     * @return bool
     */
    public function stopOnFailure()
    {
        return (bool) $this->container->getParameter('stop_on_failure');
    }

    /**
     * Skip success output?
     *
     * @return bool
     */
    public function skipSuccessOutput()
    {
        return (bool) $this->container->getParameter('skip_success_output');
    }

    /**
     * Ignore unstaged changes?
     *
     * @return bool
     */
    public function ignoreUnstagedChanges()
    {
        return (bool) $this->container->getParameter('ignore_unstaged_changes');
    }

    /**
     * Get process timeout.
     *
     * @return float
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
     * Get message by resource.
     *
     * @param string $resource
     *
     * @return string|null
     */
    public function getMessage($resource)
    {
        $message = $this->container->getParameter('message');

        if (! is_array($message) || ! array_key_exists($resource, $message)) {
            return null;
        }

        return (string) $message[$resource];
    }
}
