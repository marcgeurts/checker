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
     * Advanced progress.
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

        $result = $actionEvent->getResult();
        $this->progressBar->setMessage(str_pad($actionEvent->getAction()->getName(), 50, '.', STR_PAD_RIGHT));

        if (!$result instanceof ResultInterface) {
            $this->progressBar->setMessage('<fg=magenta>In progress</fg=magenta>', 'status');
            $this->progressBar->advance();

            return;
        }

        $this->progressBar->setOverwrite(true);

        switch ($result->getStatus()) {
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
