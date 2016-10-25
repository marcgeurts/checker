<?php

namespace ClickNow\Checker\Subscriber;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Console\Helper\PathsHelper;
use ClickNow\Checker\Event\RunnerEvent;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Result\ResultsCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReportSubscriber implements EventSubscriberInterface
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var \ClickNow\Checker\Console\Helper\PathsHelper
     */
    private $paths;

    /**
     * Progress subscriber.
     *
     * @param \ClickNow\Checker\IO\IOInterface             $io
     * @param \ClickNow\Checker\Console\Helper\PathsHelper $paths
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
        $command = $event->getContext()->getCommand();
        $warning = $results->filterByWarning();

        if ($results->isFailed()) {
            $this->reportError($command, $results->filterByError(), $warning);

            return;
        }

        if ($command->isSkipSuccessOutput()) {
            $this->reportWarning($warning);

            return;
        }

        $this->reportSuccess($command, $warning);
    }

    /**
     * Report success.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Result\ResultsCollection $warnings
     *
     * @return void
     */
    private function reportSuccess(CommandInterface $command, ResultsCollection $warnings)
    {
        $successMessage = $this->paths->getMessage($command->getMessage('successfully'));
        if ($successMessage !== null) {
            $this->io->text(sprintf('<fg=green>%s</fg=green>', $successMessage));
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
            $this->io->note($warning->getMessage());
        }
    }

    /**
     * Report error.
     *
     * @param \ClickNow\Checker\Command\CommandInterface $command
     * @param \ClickNow\Checker\Result\ResultsCollection $errors
     * @param \ClickNow\Checker\Result\ResultsCollection $warnings
     *
     * @return void
     */
    private function reportError(CommandInterface $command, ResultsCollection $errors, ResultsCollection $warnings)
    {
        $errorMessage = $this->paths->getMessage($command->getMessage('failed'));
        if ($errorMessage !== null) {
            $this->io->text(sprintf('<fg=red>%s</fg=red>', $errorMessage));
        }

        $this->reportWarning($warnings);

        foreach ($errors as $error) {
            /* @var \ClickNow\Checker\Result\ResultInterface $error */
            $this->io->error($error->getMessage());
        }
    }
}
