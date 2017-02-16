<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressBarSubscriber implements EventSubscriberInterface
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
     * Progress bar subscriber.
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
     * @return array<*,string>
     */
    public static function getSubscribedEvents()
    {
        return [
            RunnerEvent::RUNNER_RUN          => 'startProgress',
            ActionEvent::ACTION_FAILED       => 'advanceProgress',
            ActionEvent::ACTION_SUCCESSFULLY => 'advanceProgress',
            RunnerEvent::RUNNER_SUCCESSFULLY => 'finishProgress',
            RunnerEvent::RUNNER_FAILED       => 'finishProgress',
        ];
    }

    /**
     * Is enabled?
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface $runner
     *
     * @return bool
     */
    private function isEnabled(RunnerInterface $runner)
    {
        return $runner->getProgress() === 'bar';
    }

    /**
     * Start progress.
     *
     * @param \ClickNow\Checker\Event\RunnerEvent $runnerEvent
     *
     * @return void
     */
    public function startProgress(RunnerEvent $runnerEvent)
    {
        if (!$this->isEnabled($runnerEvent->getContext()->getRunner())) {
            return;
        }

        $this->progressBar->start($runnerEvent->getActions()->count());
    }

    /**
     * Advance progress.
     *
     * @param \ClickNow\Checker\Event\ActionEvent $actionEvent
     *
     * @return void
     */
    public function advanceProgress(ActionEvent $actionEvent)
    {
        if (!$this->isEnabled($actionEvent->getContext()->getRunner())) {
            return;
        }

        $this->progressBar->advance();
    }

    /**
     * Finish progress.
     *
     * @param \ClickNow\Checker\Event\RunnerEvent $runnerEvent
     *
     * @return void
     */
    public function finishProgress(RunnerEvent $runnerEvent)
    {
        if (!$this->isEnabled($runnerEvent->getContext()->getRunner())) {
            return;
        }

        if ($this->progressBar->getProgress() != $this->progressBar->getMaxSteps()) {
            $this->io->newLine(2);
            $this->io->caution('Aborted...');

            return;
        }

        $this->progressBar->finish();
        $this->io->newLine(2);
    }
}
