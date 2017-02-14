<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\Helper\PathsHelper;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\ResultsCollection;
use ClickNow\Checker\Runner\RunnerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReportSubscriber implements EventSubscriberInterface
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var \ClickNow\Checker\Helper\PathsHelper
     */
    private $paths;

    /**
     * Progress subscriber.
     *
     * @param \ClickNow\Checker\IO\IOInterface     $io
     * @param \ClickNow\Checker\Helper\PathsHelper $paths
     */
    public function __construct(IOInterface $io, PathsHelper $paths)
    {
        $this->io = $io;
        $this->paths = $paths;
    }

    /**
     * Get subscribed events.
     *
     * @return array<*,string>
     */
    public static function getSubscribedEvents()
    {
        return [
            RunnerEvent::RUNNER_SUCCESSFULLY => 'onReport',
            RunnerEvent::RUNNER_FAILED       => 'onReport',
        ];
    }

    /**
     * On report.
     *
     * @param \ClickNow\Checker\Event\RunnerEvent $event
     *
     * @return void
     */
    public function onReport(RunnerEvent $event)
    {
        $results = $event->getResults();
        if (is_null($results) || $results->isEmpty()) {
            return;
        }

        $runner = $event->getContext()->getRunner();
        $this->report($runner, $results);
    }

    /**
     * Report.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Result\ResultsCollection $results
     *
     * @return void
     */
    private function report(RunnerInterface $runner, ResultsCollection $results)
    {
        $warning = $results->filterByWarning();

        if ($results->isFailed()) {
            $this->reportError($runner, $results->filterByError(), $warning);

            return;
        }

        if ($runner->isSkipSuccessOutput()) {
            $this->reportWarning($warning);

            return;
        }

        $this->reportSuccess($runner, $warning);
    }

    /**
     * Report success.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Result\ResultsCollection $warnings
     *
     * @return void
     */
    private function reportSuccess(RunnerInterface $runner, ResultsCollection $warnings)
    {
        $successMessage = $this->paths->getMessage($runner->getMessage('successfully'));
        if ($successMessage !== null) {
            $this->io->successText($successMessage);
        }

        $this->reportWarning($warnings);
    }

    /**
     * Report warning.
     *
     * @param \ClickNow\Checker\Result\ResultsCollection $warnings
     *
     * @return void
     */
    private function reportWarning(ResultsCollection $warnings)
    {
        foreach ($warnings as $warning) {
            /* @var \ClickNow\Checker\Result\ResultInterface $warning */
            $this->io->warning($warning->getAction()->getName());
            $this->io->warningText($warning->getMessage());
        }
    }

    /**
     * Report error.
     *
     * @param \ClickNow\Checker\Runner\RunnerInterface   $runner
     * @param \ClickNow\Checker\Result\ResultsCollection $errors
     * @param \ClickNow\Checker\Result\ResultsCollection $warnings
     *
     * @return void
     */
    private function reportError(RunnerInterface $runner, ResultsCollection $errors, ResultsCollection $warnings)
    {
        $errorMessage = $this->paths->getMessage($runner->getMessage('failed'));
        if ($errorMessage !== null) {
            $this->io->errorText($errorMessage);
        }

        $this->reportWarning($warnings);

        foreach ($errors as $error) {
            /* @var \ClickNow\Checker\Result\ResultInterface $error */
            $this->io->error($error->getAction()->getName());
            $this->io->errorText($error->getMessage());
        }
    }
}
