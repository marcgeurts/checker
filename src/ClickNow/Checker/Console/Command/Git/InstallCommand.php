<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Exception\FileNotFoundException;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Util\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

class InstallCommand extends Command
{
    const GENERATED_MESSAGE = 'Checker generated this file, do not edit!';

    /**
     * @var \ClickNow\Checker\Config\Checker
     */
    private $checker;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \ClickNow\Checker\IO\IOInterface
     */
    private $io;

    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     */
    private $processBuilder;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * Install command.
     *
     * @param \ClickNow\Checker\Config\Checker          $checker
     * @param \Symfony\Component\Filesystem\Filesystem  $filesystem
     * @param \ClickNow\Checker\IO\IOInterface          $io
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder
     */
    public function __construct(
        Checker $checker,
        Filesystem $filesystem,
        IOInterface $io,
        ProcessBuilder $processBuilder
    ) {
        $this->checker = $checker;
        $this->filesystem = $filesystem;
        $this->io = $io;
        $this->processBuilder = $processBuilder;

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
            ->setName('git:install')
            ->setDescription('Install git hooks');
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
        $this->input = $input;
        $this->io->title('Checker are installing in git hooks!');
        $gitHooksDir = $this->getGitHooksDir();

        foreach (Git::$hooks as $hook) {
            $hookPath = $gitHooksDir.$hook;
            $hookTemplate = $this->getHookTemplate($hook);
            $this->backupGitHook($hookPath);
            $this->createGitHook($hook, $hookPath, $hookTemplate);
        }

        $this->io->success('Checker was installed in git hooks successfully! Very nice...');
    }

    /**
     * Get git hooks directory.
     *
     * @return string
     */
    private function getGitHooksDir()
    {
        $gitHooksDir = $this->paths()->getGitHooksDir();

        if (!$this->filesystem->exists($gitHooksDir)) {
            $this->filesystem->mkdir($gitHooksDir);
            $this->io->note(sprintf('Created git hooks folder at: `%s`.', $gitHooksDir));
        }

        return $gitHooksDir;
    }

    /**
     * Get hook template.
     *
     * @param string $hook
     *
     * @throws \ClickNow\Checker\Exception\FileNotFoundException
     *
     * @return string
     */
    private function getHookTemplate($hook)
    {
        $resourceHooksPath = $this->paths()->getGitHookTemplatesDir().$this->checker->getHooksPreset();
        $resourceHooksPath = $this->paths()->getPathWithTrailingSlash($resourceHooksPath);
        $hookTemplate = $resourceHooksPath.'all';

        if ($this->filesystem->exists($resourceHooksPath.$hook)) {
            $hookTemplate = $resourceHooksPath.$hook;
        }

        $hookTemplate = $this->getCustomHookTemplate($hook, $hookTemplate);

        if (!$this->filesystem->exists($hookTemplate)) {
            throw new FileNotFoundException(
                sprintf('Could not find hook template for `%s` at `%s`.', $hook, $hookTemplate)
            );
        }

        return $hookTemplate;
    }

    /**
     * Get custom hook template.
     *
     * @param string $hook
     * @param string $defaultTemplate
     *
     * @return string
     */
    private function getCustomHookTemplate($hook, $defaultTemplate)
    {
        $customHooksPath = $this->paths()->getPathWithTrailingSlash($this->checker->getHooksDir());

        if (!$customHooksPath) {
            return $defaultTemplate;
        }

        if ($this->filesystem->exists($customHooksPath.$hook)) {
            return $customHooksPath.$hook;
        }

        if ($this->filesystem->exists($customHooksPath.'all')) {
            return $customHooksPath.'all';
        }

        return $defaultTemplate;
    }

    /**
     * Backup git hook.
     *
     * @param string $hookPath
     *
     * @return void
     */
    private function backupGitHook($hookPath)
    {
        if (!$this->filesystem->exists($hookPath)) {
            return;
        }

        $content = file_get_contents($hookPath);

        if (strpos($content, self::GENERATED_MESSAGE)) {
            return;
        }

        $this->io->log(sprintf('Checker backup git hook `%s` to `%s`.', $hookPath, $hookPath.'.checker'));
        $this->filesystem->rename($hookPath, $hookPath.'.checker', true);
    }

    /**
     * Create git hook.
     *
     * @param string $hook
     * @param string $hookPath
     * @param string $hookTemplate
     *
     * @return void
     */
    private function createGitHook($hook, $hookPath, $hookTemplate)
    {
        $this->io->log(sprintf('Checker create git hook `%s`.', $hookPath));

        $content = file_get_contents($hookTemplate);
        $replacements = [
            '$(GENERATED_MESSAGE)' => self::GENERATED_MESSAGE,
            '${HOOK_EXEC_PATH}'    => $this->paths()->getGitHookExecutionPath(),
            '$(HOOK_COMMAND)'      => $this->generateHookCommand('git:'.$hook),
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        file_put_contents($hookPath, $content);
        $this->filesystem->chmod($hookPath, 0775);
    }

    /**
     * Generate hook command.
     *
     * @param string $command
     *
     * @return string
     */
    private function generateHookCommand($command)
    {
        $executable = $this->paths()->getBinCommand('checker', true);

        $this->processBuilder->setArguments([
            $this->paths()->getRelativeProjectPath($executable),
            $command,
        ]);

        $configFile = $this->useExoticConfigFile();
        if ($configFile !== null) {
            $this->processBuilder->add(sprintf('--config=%s', $configFile));
        }

        return $this->processBuilder->getProcess()->getCommandLine();
    }

    /**
     * Use exotic config file.
     *
     * @return null|string
     */
    private function useExoticConfigFile()
    {
        try {
            $configPath = $this->paths()->getAbsolutePath($this->input->getOption('config'));
            if ($configPath != $this->paths()->getDefaultConfigPath()) {
                return $this->paths()->getRelativeProjectPath($configPath);
            }
        } catch (FileNotFoundException $e) {
            // no config file should be set.
        }

        return null;
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
