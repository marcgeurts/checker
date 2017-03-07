<?php

namespace ClickNow\Checker\Console\Command\Git;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Exception\FileNotFoundException;
use ClickNow\Checker\IO\IOInterface;
use ClickNow\Checker\Repository\Filesystem;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class InstallCommand extends Command
{
    const GENERATED_MESSAGE = 'Checker generated this file, do not edit!';

    /**
     * @var \ClickNow\Checker\Config\Checker
     */
    private $checker;

    /**
     * @var \ClickNow\Checker\Repository\Filesystem
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
     * @var array
     */
    private $gitHooks;

    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * Install command.
     *
     * @param \ClickNow\Checker\Config\Checker          $checker
     * @param \ClickNow\Checker\Repository\Filesystem   $filesystem
     * @param \ClickNow\Checker\IO\IOInterface          $io
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder
     * @param array                                     $gitHooks
     */
    public function __construct(
        Checker $checker,
        Filesystem $filesystem,
        IOInterface $io,
        ProcessBuilder $processBuilder,
        array $gitHooks
    ) {
        $this->checker = $checker;
        $this->filesystem = $filesystem;
        $this->io = $io;
        $this->processBuilder = $processBuilder;
        $this->gitHooks = array_keys($gitHooks);

        parent::__construct('git:install');

        $this->setDescription('Install git hooks');
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

        foreach ($this->gitHooks as $gitHook) {
            $gitHookPath = $gitHooksDir.$gitHook;
            $hookTemplate = $this->getHookTemplate($gitHook);
            $this->backupGitHook($gitHookPath);
            $this->createGitHook($gitHook, $gitHookPath, $hookTemplate);
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
        $gitHooksDir = $this->getPathsHelper()->getGitHooksDir();

        if (!$this->filesystem->exists($gitHooksDir)) {
            $this->filesystem->mkdir($gitHooksDir);
            $this->io->note(sprintf('Created git hooks folder at: `%s`.', $gitHooksDir));
        }

        return $gitHooksDir;
    }

    /**
     * Get hook template.
     *
     * @param string $name
     *
     * @throws \ClickNow\Checker\Exception\FileNotFoundException
     *
     * @return SplFileInfo
     */
    private function getHookTemplate($name)
    {
        $resourcePath = $this->getPathsHelper()->getGitHookTemplatesDir().$this->checker->getHooksPreset();
        $customPath = $this->getPathsHelper()->getPathWithTrailingSlash($this->checker->getHooksDir());
        $template = $this->getPathsHelper()->getPathWithTrailingSlash($resourcePath).$name;

        if ($customPath && $this->filesystem->exists($customPath.$name)) {
            $template = $customPath.$name;
        }

        if (!$this->filesystem->exists($template)) {
            throw new FileNotFoundException(sprintf('Could not find template for `%s` at `%s`.', $name, $template));
        }

        return new SplFileInfo($template);
    }

    /**
     * Backup git hook.
     *
     * @param string $path
     *
     * @return void
     */
    private function backupGitHook($path)
    {
        if (!$this->filesystem->exists($path)) {
            return;
        }

        $content = $this->filesystem->readFromFileInfo(new SplFileInfo($path));

        if (strpos($content, self::GENERATED_MESSAGE) !== false) {
            return;
        }

        $this->io->log(sprintf('Checker backup git hook `%s` to `%s`.', $path, $path.'.checker'));
        $this->filesystem->rename($path, $path.'.checker', true);
    }

    /**
     * Create git hook.
     *
     * @param string      $name
     * @param string      $path
     * @param SplFileInfo $template
     *
     * @return void
     */
    private function createGitHook($name, $path, SplFileInfo $template)
    {
        $this->io->log(sprintf('Checker create git hook `%s`.', $path));

        $content = $this->filesystem->readFromFileInfo($template);
        $replacements = [
            '$(GENERATED_MESSAGE)' => self::GENERATED_MESSAGE,
            '${HOOK_EXEC_PATH}'    => $this->getPathsHelper()->getGitHookExecutionPath(),
            '$(HOOK_COMMAND)'      => $this->generateHookCommand('git:'.$name),
        ];

        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        $this->filesystem->dumpFile($path, $content);
        $this->filesystem->chmod($path, 0775);
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
        $executable = $this->getPharCommand() ?: $this->getPathsHelper()->getBinCommand('checker', true);

        $this->processBuilder->setArguments([
            $this->getPathsHelper()->getRelativeProjectPath($executable),
            $command,
        ]);

        $configPath = $this->useExoticConfigPath();
        if ($configPath !== null) {
            $this->processBuilder->add(sprintf('--config=%s', $configPath));
        }

        return $this->processBuilder->getProcess()->getCommandLine();
    }

    /**
     * Get phar command.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @codeCoverageIgnore
     */
    private function getPharCommand()
    {
        $token = $_SERVER['argv'][0];

        if (pathinfo($token, PATHINFO_EXTENSION) == 'phar') {
            return $token;
        }

        return '';
    }

    /**
     * Use exotic config path.
     *
     * @return null|string
     */
    private function useExoticConfigPath()
    {
        try {
            $configPath = $this->getPathsHelper()->getAbsolutePath($this->input->getOption('config'));
            if ($configPath != $this->getPathsHelper()->getDefaultConfigPath()) {
                return $this->getPathsHelper()->getRelativeProjectPath($configPath);
            }
        } catch (FileNotFoundException $e) {
            // no config path
        }

        return null;
    }

    /**
     * Get paths helper.
     *
     * @return \ClickNow\Checker\Helper\PathsHelper|\Symfony\Component\Console\Helper\HelperInterface
     */
    private function getPathsHelper()
    {
        return $this->getHelperSet()->get('paths');
    }
}
