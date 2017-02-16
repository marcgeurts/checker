<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Event\ActionEvent;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressListSubscriber implements EventSubscriberInterface
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
     * Progress list subscriber.
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
            ActionEvent::ACTION_RUN          => 'advanceProgress',
            ActionEvent::ACTION_FAILED       => 'changeProgress',
            ActionEvent::ACTION_SUCCESSFULLY => 'changeProgress',
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
        return $runner->getProgress() === 'list';
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

        $this->progressBar->setFormat('<fg=cyan>Running %current%/%max%:</fg=cyan> %message% %status%');
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

        $this->progressBar->setMessage(str_pad($actionEvent->getAction()->getName(), 50, '.', STR_PAD_RIGHT));
        $this->progressBar->setMessage('<fg=magenta>In progress</fg=magenta>', 'status');
        $this->progressBar->advance();
    }

    /**
     * Change progress.
     *
     * @param \ClickNow\Checker\Event\ActionEvent $actionEvent
     *
     * @return void
     */
    public function changeProgress(ActionEvent $actionEvent)
    {
        if (!$this->isEnabled($actionEvent->getContext()->getRunner())) {
            return;
        }

        $this->progressBar->setMessage(str_pad($actionEvent->getAction()->getName(), 50, '.', STR_PAD_RIGHT));
        $this->progressBar->setOverwrite(true);

        switch ($actionEvent->getResult()->getStatus()) {
            case ResultInterface::SUCCESS:
                $status = '<fg=green>Ok</fg=green>';
                break;

            case ResultInterface::WARNING:
                $status = '<fg=yellow>Warning</fg=yellow>';
                break;

            case ResultInterface::ERROR:
                $status = '<fg=red>Error</fg=red>';
                break;

            default:
                $status = '<fg=cyan>Skipped</fg=cyan>';
                break;
        }

        $this->progressBar->setMessage($status, 'status');
        $this->progressBar->display();
        $this->progressBar->setOverwrite(false);
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
