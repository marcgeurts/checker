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
        $this->io = $io;
        $this->repository = $repository;
    }

    /**
     * Get subscribed events.
     *
     * @return array<*,array<string|integer>>
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
     *
     * @return void
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

        $this->runSaveStash();
    }

    /**
     * Pop stash.
     *
     * @throws \ClickNow\Checker\Exception\RuntimeException
     *
     * @return void
     */
    public function popStash()
    {
        if (!$this->stashIsApplied) {
            return;
        }

        $this->stashIsApplied = false;
        $this->runPopStash();
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
        return $context->getCommand()->isIgnoreUnstagedChanges();
    }

    /**
     * Run save stash.
     *
     * @return void
     */
    private function runSaveStash()
    {
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
     * Run pop stash.
     *
     * @return void
     */
    private function runPopStash()
    {
        try {
            $this->io->note('Reapplying unstaged changes from stash.');
            $this->repository->run('stash', ['pop', '--quiet']);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(
                'The stashed changes could not be applied. Please run `git stash pop` manually! More info: %s',
                $e->__toString()
            ), 0, $e);
        }
    }

    /**
     * Register shutdown handler.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    private function registerShutdownHandler()
    {
        if ($this->shutdownFunctionRegistered) {
            return;
        }

        $subscriber = $this;

        register_shutdown_function(function () use ($subscriber) {
            $error = error_get_last();

            if (!$error || in_array($error['type'], [E_DEPRECATED, E_USER_DEPRECATED])) {
                return;
            }

            $subscriber->popStash();
        });

        $this->shutdownFunctionRegistered = true;
    }
}
