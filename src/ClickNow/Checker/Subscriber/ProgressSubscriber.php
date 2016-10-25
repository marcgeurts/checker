<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressSubscriber implements EventSubscriberInterface
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    private $progressBar;

    /**
     * Progress subscriber.
     *
     * @param \ClickNow\Checker\IO\IOInterface $io
     */
    public function __construct(IOInterface $io)
    {
        $this->io = $io;
        $this->progressBar = $this->io->createProgressBar();
    }

    /**
     * Get subscribed events.
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents()
    {
        return [
            RunnerEvent::RUNNER_RUN          => 'startProgress',
            ActionEvent::ACTION_RUN          => 'advanceProgress',
            RunnerEvent::RUNNER_SUCCESSFULLY => 'finishProgress',
            RunnerEvent::RUNNER_FAILED       => 'finishProgress',
        ];
    }

    /**
     * Start progress.
     *
     * @param \ClickNow\Checker\Event\RunnerEvent $event
     */
    public function startProgress(RunnerEvent $event)
    {
        $this->progressBar->start($event->getActions()->count());
    }

    /**
     * Advance progress.
     */
    public function advanceProgress()
    {
        $this->progressBar->advance();
    }

    /**
     * Finish progress.
     */
    public function finishProgress()
    {
        if ($this->progressBar->getProgress() != $this->progressBar->getMaxSteps()) {
            $this->io->newLine(2);
            $this->io->caution('Aborted...');

            return;
        }

        $this->progressBar->finish();
        $this->io->newLine(2);
    }
}
