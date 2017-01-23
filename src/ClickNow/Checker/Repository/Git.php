<?php

namespace ClickNow\Checker\Repository;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Repository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\ProcessBuilder;

class Git
{
    /**
     * @var \Gitonomy\Git\Repository
     */
    private $repository;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Git.
     *
     * @param \Gitonomy\Git\Repository                 $repository
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Repository $repository, Filesystem $filesystem)
    {
        $this->repository = $repository;
        $this->filesystem = $filesystem;
    }

    public function getUserName()
    {
        $pb = new ProcessBuilder(['git', 'config', 'user.name']);
        $p = $pb->getProcess();
        $p->run();
        if(!$p->isSuccessful()) {
            return null;
        }
        return $p->getOutput();
    }

    public function getUserEmail()
    {
        $pb = new ProcessBuilder(['git', 'config', 'user.email']);
        $p = $pb->getProcess();
        $p->run();
        if(!$p->isSuccessful()) {
            return null;
        }
        return $p->getOutput();
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
        $files = new FilesCollection();

        foreach ($filePaths as $file) {
            $files->add(new SplFileInfo($file, dirname($file), $file));
        }

        return $files;
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

            return $this->parseFilesFromDiff($diff);
        }

        $diff = $this->repository->getWorkingCopy()->getDiffStaged();

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

            return $this->parseFilesFromDiff($diff);
        }

        $diff = $this->repository->getWorkingCopy()->getDiffStaged();

        return $this->parseFilesFromDiff($diff);
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
     * Get SplFileInfo.
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
}
