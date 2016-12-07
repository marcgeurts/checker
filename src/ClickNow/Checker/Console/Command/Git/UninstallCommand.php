<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Repository\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class UninstallCommand extends Command
{
    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * Uninstall command.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \ClickNow\Checker\IO\IOInterface         $io
     */
    public function __construct(Filesystem $filesystem, IOInterface $io)
    {
        $this->filesystem = $filesystem;
        $this->io = $io;

        parent::__construct();
    }

    /**
     * Configure.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('git:uninstall')
            ->setDescription('Uninstall git hooks');
    }

    /**
     * Execute.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Checker are uninstalling in git hooks!');

        $gitHooksDir = $this->paths()->getGitHooksDir();

        foreach (Git::$hooks as $hook) {
            $hookPath = $gitHooksDir.$hook;
            $this->removeGitHook($hookPath);
            $this->restoreGitHook($hookPath);
        }

        $this->io->note('To install again use the command `git:install`.');
        $this->io->success('Checker was uninstalled in git hooks successfully! Too bad...');
    }

    /**
     * Remove git hook.
     *
     * @param string $hookPath
     *
     * @return void
     */
    private function removeGitHook($hookPath)
    {
        if (!$this->filesystem->exists($hookPath)) {
            return;
        }

        $this->io->log(sprintf('Checker remove git hook `%s`.', $hookPath));
        $this->filesystem->remove($hookPath);
    }

    /**
     * Restore git hook.
     *
     * @param string $hookPath
     *
     * @return void
     */
    private function restoreGitHook($hookPath)
    {
        if (!$this->filesystem->exists($hookPath.'.checker')) {
            return;
        }

        $this->io->log(sprintf('Checker restore git hook `%s` to `%s`.', $hookPath.'.checker', $hookPath));
        $this->filesystem->rename($hookPath.'.checker', $hookPath);
    }

    /**
     * Paths helper.
     *
     * @return \ClickNow\Checker\Console\Helper\PathsHelper
     */
    private function paths()
    {
        return $this->getHelperSet()->get('paths');
    }
}
