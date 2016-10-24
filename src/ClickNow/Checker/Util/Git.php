<?php

namespace ClickNow\Checker\Util;

use Gitonomy\Git\Diff\Diff;
use Gitonomy\Git\Diff\File;
use Gitonomy\Git\Repository;
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
     * Git.
     *
     * @param \Gitonomy\Git\Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get registered files.
     *
     * @return \ClickNow\Checker\Util\FilesCollection
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
     * @param $rawDiff
     *
     * @return \ClickNow\Checker\Util\FilesCollection
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
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    private function parseFilesFromDiff(Diff $diff)
    {
        $files = new FilesCollection($diff->getFiles());
        $files = $files->filter(function (File $file) {
            return ! $file->isDeletion();
        });

        return $files->map(function (File $file) {
            $fileName = $file->isRename() ? $file->getNewName() : $file->getName();

            return new SplFileInfo($fileName, dirname($fileName), $fileName);
        });
    }
}
