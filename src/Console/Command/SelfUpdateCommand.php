<?php

namespace ClickNow\Checker\Console\Command;

use ClickNow\Checker\Console\Application;
use ClickNow\Checker\IO\IOInterface;
use Exception;
use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var \Humbug\SelfUpdate\Updater
     */
    private $updater;

    /**
     * Self update command.
     *
     * @param \ClickNow\Checker\IO\IOInterface $io
     * @param \Humbug\SelfUpdate\Updater       $updater
     */
    public function __construct(IOInterface $io, Updater $updater)
    {
        $this->io = $io;
        $this->updater = $updater;

        parent::__construct('self-update');

        $this->setAliases(['selfupdate']);
        $this->setDescription('Automatically update the PHAR file.');
    }

    /**
     * Execute.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Checker is being updated!');

        /** @var \Humbug\SelfUpdate\Strategy\GithubStrategy $strategy */
        $strategy = $this->updater->getStrategy();
        $strategy->setPackageName(Application::PACKAGE_NAME);
        $strategy->setPharName('checker.phar');
        $strategy->setCurrentLocalVersion(Application::APP_VERSION);

        try {
            $this->displayResult($this->updater->update());

            return 0;
        } catch (Exception $e) {
            $this->io->error($e->getMessage());

            return 1;
        }
    }

    /**
     * Display result.
     *
     * @param bool $updated
     *
     * @return void
     */
    private function displayResult($updated)
    {
        if ($updated) {
            $this->io->success('PHAR file updated successfully!');

            return;
        }

        $this->io->note('No need to update.');
    }
}
