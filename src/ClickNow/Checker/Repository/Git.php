<?php

namespace ClickNow\Checker\Repository;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Repository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class Git
{
    /**
     * @var array
     */
    public static $hooks = [
        'applypatch-msg',
        'pre-applypatch',
        'post-applypatch',
        'pre-commit',
        'prepare-commit-msg',
        'commit-msg',
        'post-commit',
        'pre-rebase',
        'post-checkout',
        'post-merge',
        'pre-push',
        'pre-receive',
        'update',
        'post-receive',
        'post-update',
        'pre-auto-gc',
        'post-rewrite',
    ];

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
            if ($file->isDeletion()) {
                continue;
            }

            $fileName = $file->isRename() ? $file->getNewName() : $file->getName();
            $fileObject = new SplFileInfo($fileName, dirname($fileName), $fileName);

            if ($this->filesystem->exists($fileObject->getPathname())) {
                $files->add($fileObject);
            }
        }

        return $files;
    }
}
