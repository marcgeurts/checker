<?php

namespace ClickNow\Checker\Console\Helper;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Exception\DirectoryNotFoundException;
use ClickNow\Checker\Exception\FileNotFoundException;
use ClickNow\Checker\Process\ExecutableFinder;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Filesystem\Filesystem;

class PathsHelper extends Helper
{
    const HELPER_NAME = 'paths';

    /**
     * @var \ClickNow\Checker\Config\Checker
     */
    private $checker;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \ClickNow\Checker\Process\ExecutableFinder
     */
    private $executableFinder;

    /**
     * @var string
     */
    private $defaultConfigPath;

    /**
     * Paths helper.
     *
     * @param \ClickNow\Checker\Config\Checker           $checker
     * @param \Symfony\Component\Filesystem\Filesystem   $filesystem
     * @param \ClickNow\Checker\Process\ExecutableFinder $executableFinder
     * @param string                                     $defaultConfigPath
     */
    public function __construct(
        Checker $checker,
        Filesystem $filesystem,
        ExecutableFinder $executableFinder,
        $defaultConfigPath
    ) {
        $this->checker = $checker;
        $this->filesystem = $filesystem;
        $this->executableFinder = $executableFinder;
        $this->defaultConfigPath = $defaultConfigPath;
    }

    /**
     * Get project path.
     *
     * @return string
     */
    public function getProjectPath()
    {
        return $this->getRelativePath(__DIR__.'/../../../../');
    }

    /**
     * Get resources path.
     *
     * @return string
     */
    public function getResourcesPath()
    {
        return $this->getProjectPath().'resources/';
    }

    /**
     * Get ascii path.
     *
     * @return string
     */
    public function getAsciiPath()
    {
        return $this->getResourcesPath().'ascii/';
    }

    /**
     * Get message by resource.
     *
     * @param string|null $resource
     *
     * @return string|null
     */
    public function getMessage($resource)
    {
        if (empty($resource)) {
            return null;
        }

        // File specified by user
        if ($this->filesystem->exists($resource)) {
            return file_get_contents($resource);
        }

        // Embeded ASCII
        $embeddedFile = $this->getAsciiPath().$resource;
        if ($this->filesystem->exists($embeddedFile)) {
            return file_get_contents($embeddedFile);
        }

        // Simple message text
        return $resource;
    }

    /**
     * Get working directory.
     *
     * @return string
     */
    public function getWorkingDir()
    {
        return getcwd();
    }

    /**
     * Get git directory.
     *
     * @throws \ClickNow\Checker\Exception\DirectoryNotFoundException
     *
     * @return string
     */
    public function getGitDir()
    {
        $gitDir = $this->checker->getGitDir();
        if (!$this->filesystem->exists($gitDir)) {
            throw new DirectoryNotFoundException($gitDir, sprintf(
                'The configured GIT directory `%s` could not be found.',
                $gitDir
            ));
        }

        return $this->getRelativePath($gitDir);
    }

    /**
     * Get git hook execution path.
     *
     * @return string
     */
    public function getGitHookExecutionPath()
    {
        $gitPath = $this->getGitDir();

        return $this->filesystem->makePathRelative($this->getWorkingDir(), $this->getAbsolutePath($gitPath));
    }

    /**
     * Get git hooks directory.
     *
     * @return string
     */
    public function getGitHooksDir()
    {
        return $this->getGitDir().'.git/hooks/';
    }

    /**
     * The folder with all git hooks.
     *
     * @return string
     */
    public function getGitHookTemplatesDir()
    {
        return $this->getResourcesPath().'hooks/';
    }

    /**
     * Get bin directory.
     *
     * @throws \ClickNow\Checker\Exception\DirectoryNotFoundException
     *
     * @return string
     */
    public function getBinDir()
    {
        $binDir = $this->checker->getBinDir();
        if (!$this->filesystem->exists($binDir)) {
            throw new DirectoryNotFoundException($binDir, sprintf(
                'The configured BIN directory `%s` could not be found.',
                $binDir
            ));
        }

        return $this->getRelativePath($binDir);
    }

    /**
     * Get bin command.
     *
     * @param string $command
     * @param bool   $forceUnix
     *
     * @return string
     */
    public function getBinCommand($command, $forceUnix = false)
    {
        return $this->executableFinder->find($command, $forceUnix);
    }

    /**
     * Get relative path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getRelativePath($path)
    {
        $realpath = $this->getAbsolutePath($path);

        return $this->filesystem->makePathRelative($realpath, $this->getWorkingDir());
    }

    /**
     * Get relative project path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getRelativeProjectPath($path)
    {
        $realPath = $this->getAbsolutePath($path);
        $gitPath = $this->getAbsolutePath($this->getGitDir());

        if (0 !== strpos($realPath, $gitPath)) {
            return $realPath;
        }

        return rtrim($this->getRelativePath($realPath), '\\/');
    }

    /**
     * Get absolute path.
     *
     * @param string $path
     *
     * @throws \ClickNow\Checker\Exception\FileNotFoundException
     *
     * @return string
     */
    public function getAbsolutePath($path)
    {
        $path = trim($path);
        $realpath = realpath($path);

        if ($realpath === false) {
            throw new FileNotFoundException($path);
        }

        return $realpath;
    }

    /**
     * Get path with trailing slash.
     *
     * @param string $path
     *
     * @return string
     */
    public function getPathWithTrailingSlash($path)
    {
        if (!$path) {
            return $path;
        }

        return rtrim($path, '/').'/';
    }

    /**
     * Get default config path.
     *
     * @return string
     */
    public function getDefaultConfigPath()
    {
        return $this->defaultConfigPath;
    }

    /**
     * Get helper name.
     *
     * @return string
     */
    public function getName()
    {
        return self::HELPER_NAME;
    }
}
