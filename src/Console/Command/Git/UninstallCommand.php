<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Repository\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UninstallCommand extends Command
{
    /**
     * @var \ClickNow\Checker\Repository\Filesystem
     */
    private $filesystem;

    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var array
     */
    private $gitHooks;

    /**
     * Uninstall command.
     *
     * @param \ClickNow\Checker\Repository\Filesystem  $filesystem
     * @param \ClickNow\Checker\IO\IOInterface         $io
     * @param array                                    $gitHooks
     */
    public function __construct(Filesystem $filesystem, IOInterface $io, array $gitHooks)
    {
        $this->filesystem = $filesystem;
        $this->io = $io;
        $this->gitHooks = array_keys($gitHooks);

        parent::__construct('git:uninstall');

        $this->setDescription('Uninstall git hooks');
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
        $gitHooksDir = $this->getPathsHelper()->getGitHooksDir();

        foreach ($this->gitHooks as $gitHook) {
            $gitHookPath = $gitHooksDir.$gitHook;
            $this->removeGitHook($gitHookPath);
            $this->restoreGitHook($gitHookPath);
        }

        $this->io->note('To install again use the command `git:install`.');
        $this->io->success('Checker was uninstalled in git hooks successfully! Too bad...');
    }

    /**
     * Remove git hook.
     *
     * @param string $path
     *
     * @return void
     */
    private function removeGitHook($path)
    {
        if (!$this->filesystem->exists($path)) {
            return;
        }

        $this->io->log(sprintf('Checker remove git hook `%s`.', $path));
        $this->filesystem->remove($path);
    }

    /**
     * Restore git hook.
     *
     * @param string $path
     *
     * @return void
     */
    private function restoreGitHook($path)
    {
        if (!$this->filesystem->exists($path.'.checker')) {
            return;
        }

        $this->io->log(sprintf('Checker restore git hook `%s` to `%s`.', $path.'.checker', $path));
        $this->filesystem->rename($path.'.checker', $path);
    }

    /**
     * Get paths helper.
     *
     * @return \ClickNow\Checker\Helper\PathsHelper
     */
    private function getPathsHelper()
    {
        return $this->getHelperSet()->get('paths');
    }
}
