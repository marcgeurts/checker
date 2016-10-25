<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\IO\IOInterface;
use Exception;
use Gitonomy\Git\Repository;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StashUnstagedChangesSubscriber implements EventSubscriberInterface
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var \Gitonomy\Git\Repository
     */
    private $repository;

    /**
     * @var bool
     */
    private $stashIsApplied = false;

    /**
     * @var bool
     */
    private $shutdownFunctionRegistered = false;

    /**
     * Stash unstaged changes subscriber.
     *
     * @param \ClickNow\Checker\IO\IOInterface $io
     * @param \Gitonomy\Git\Repository         $repository
     */
    public function __construct(IOInterface $io, Repository $repository)
    {
        $this->repository = $repository;
        $this->io = $io;
    }

    /**
     * Get subscribed events.
     *
     * @return array<string,array<string|int>>
     */
    public static function getSubscribedEvents()
    {
        return [
            RunnerEvent::RUNNER_RUN          => ['saveStash', 10000],
            RunnerEvent::RUNNER_SUCCESSFULLY => ['popStash', -10000],
            RunnerEvent::RUNNER_FAILED       => ['popStash', -10000],
            ConsoleEvents::EXCEPTION         => ['popStash', -10000],
        ];
    }

    /**
     * Save stash.
     *
     * @param \ClickNow\Checker\Event\RunnerEvent $event
     */
    public function saveStash(RunnerEvent $event)
    {
        if (!$this->isStashEnabled($event->getContext())) {
            return;
        }

        $pending = $this->repository->getWorkingCopy()->getDiffPending();
        if (!count($pending->getFiles())) {
            return;
        }

        try {
            $this->io->note('Detected unstaged changes... Stashing them!');
            $this->repository->run('stash', ['save', '--quiet', '--keep-index', uniqid('checker')]);
        } catch (Exception $e) {
            $this->io->warning(sprintf('Failed stashing changes: %s', $e->getMessage()));

            return;
        }

        $this->stashIsApplied = true;
        $this->registerShutdownHandler();
    }

    /**
     * Pop stash.
     *
     * @throws \ClickNow\Checker\Exception\RuntimeException
     */
    public function popStash()
    {
        if (!$this->stashIsApplied) {
            return;
        }

        try {
            $this->io->note('Reapplying unstaged changes from stash.');
            $this->repository->run('stash', ['pop', '--quiet']);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(
                'The stashed changes could not be applied. Please run `git stash pop` manually! More info: %s',
                $e->__toString()
            ), 0, $e);
        }

        $this->stashIsApplied = false;
    }

    /**
     * Is stash enabled?
     *
     * @param \ClickNow\Checker\Context\ContextInterface $context
     *
     * @return bool
     */
    private function isStashEnabled(ContextInterface $context)
    {
        return $context->getCommand()->ignoreUnstagedChanges();
    }

    /**
     * Register shutdown handler.
     */
    private function registerShutdownHandler()
    {
        if ($this->shutdownFunctionRegistered) {
            return;
        }

        $subscriber = $this;

        register_shutdown_function(function () use ($subscriber) {
            if (!error_get_last()) {
                return;
            }
            $subscriber->popStash();
        });

        $this->shutdownFunctionRegistered = true;
    }
}
