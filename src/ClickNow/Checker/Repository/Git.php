<?php

namespace ClickNow\Checker\Repository;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Repository;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

class Git
{
    /**
     * @var \Gitonomy\Git\Repository
     */
    private $repository;

    /**
     * @var \ClickNow\Checker\Repository\Filesystem
     */
    private $filesystem;

    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     */
    private $processBuilder;

    /**
     * Git.
     *
     * @param \Gitonomy\Git\Repository                  $repository
     * @param \ClickNow\Checker\Repository\Filesystem   $filesystem
     * @param \Symfony\Component\Process\ProcessBuilder $processBuilder
     */
    public function __construct(Repository $repository, Filesystem $filesystem, ProcessBuilder $processBuilder)
    {
        $this->repository = $repository;
        $this->filesystem = $filesystem;
        $this->processBuilder = $processBuilder;
    }

    /**
     * Get commit message
     *
     * @return null|string
     */
    public function getCommitMessage()
    {
        $commitMessageFile = $this->repository->getGitDir().'/COMMIT_EDITMSG';

        if (!$this->filesystem->exists($commitMessageFile)) {
            return null;
        }

        return $this->filesystem->readFromFileInfo(new \SplFileInfo($commitMessageFile));
    }

    /**
     * Get user name.
     *
     * @return null|string
     */
    public function getUserName()
    {
        return $this->runProcess(['git', 'config', 'user.name']);
    }

    /**
     * Get user e-mail.
     *
     * @return null|string
     */
    public function getUserEmail()
    {
        return $this->runProcess(['git', 'config', 'user.email']);
    }

    /**
     * Get registered files.
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function getRegisteredFiles()
    {
        $allFiles = trim($this->repository->run('ls-files'));
        $filePaths = preg_split('/\r\n|\n|\r/', $allFiles);

        return $this->parseFilesPaths($filePaths);
    }

    /**
     * Get changed files.
     *
     * @param string $rawDiff
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function getChangedFiles($rawDiff)
    {
        if ($rawDiff) {
            $diff = Diff::parse($rawDiff);
            $diff->setRepository($this->repository);
        } else {
            $diff = $this->repository->getWorkingCopy()->getDiffStaged();
        }

        return $this->parseFilesFromDiff($diff);
    }

    /**
     * Get committed files.
     *
     * @param string $rawDiff
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function getCommittedFiles($rawDiff)
    {
        if ($rawDiff) {
            $diff = Diff::parse($rawDiff);
            $diff->setRepository($this->repository);
        } else {
            $diff = $this->repository->getDiff(['@{u}', 'HEAD']);
        }

        return $this->parseFilesFromDiff($diff);
    }

    /**
     * Parse files paths
     *
     * @param array $filePaths
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    private function parseFilesPaths(array $filePaths)
    {
        $files = new FilesCollection();

        foreach ($filePaths as $file) {
            $files->add(new SplFileInfo($file, dirname($file), $file));
        }

        return $files;
    }

    /**
     * Parse files from diff.
     *
     * @param \Gitonomy\Git\Diff\Diff $diff
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    private function parseFilesFromDiff(Diff $diff)
    {
        $files = new FilesCollection();

        /* @var \Gitonomy\Git\Diff\File $file */
        foreach ($diff->getFiles() as $file) {
            $splFileInfo = $this->getSplFileInfo($file);

            if (!$file->isDeletion() && $this->filesystem->exists($splFileInfo->getPathname())) {
                $files->add($splFileInfo);
            }
        }

        return $files;
    }

    /**
     * Get spl file info.
     *
     * @param \Gitonomy\Git\Diff\File $file
     *
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    private function getSplFileInfo(File $file)
    {
        $name = $file->isRename() ? $file->getNewName() : $file->getName();

        return new SplFileInfo($name, dirname($name), $name);
    }

    /**
     * Run process.
     *
     * @param array $arguments
     *
     * @return null|string
     */
    private function runProcess(array $arguments)
    {
        $process = $this->processBuilder->setArguments($arguments)->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        return $process->getOutput();
    }
}
