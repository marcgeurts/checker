<?php

namespace ClickNow\Checker\Util;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Finder\Comparator\DateComparator;
use Symfony\Component\Finder\Comparator\NumberComparator;
use Symfony\Component\Finder\Iterator\CustomFilterIterator;
use Symfony\Component\Finder\Iterator\DateRangeFilterIterator;
use Symfony\Component\Finder\Iterator\FilenameFilterIterator;
use Symfony\Component\Finder\Iterator\PathFilterIterator;
use Symfony\Component\Finder\Iterator\SizeRangeFilterIterator;
use Symfony\Component\Finder\SplFileInfo;
use Traversable;

class FilesCollection extends ArrayCollection
{
    /**
     * Filter by name.
     *
     * @param string $pattern
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function name($pattern)
    {
        return new self(iterator_to_array(
            new FilenameFilterIterator($this->getIterator(), [$pattern], [])
        ));
    }

    /**
     * Filter by not path.
     *
     * @param string $pattern
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function notName($pattern)
    {
        return new self(iterator_to_array(
            new FilenameFilterIterator($this->getIterator(), [], [$pattern])
        ));
    }

    /**
     * Filter by path.
     *
     * @param string $pattern
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function path($pattern)
    {
        return new self(iterator_to_array(
            new PathFilterIterator($this->getIterator(), [$pattern], [])
        ));
    }

    /**
     * Filter by not path.
     *
     * @param string $pattern
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function notPath($pattern)
    {
        return new self(iterator_to_array(
            new PathFilterIterator($this->getIterator(), [], [$pattern])
        ));
    }

    /**
     * Filter by extension.
     *
     * @param array $extensions
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function extensions(array $extensions)
    {
        if (count($extensions) < 1) {
            return new self();
        }

        return $this->name(sprintf('/\.(%s)$/i', implode('|', $extensions)));
    }

    /**
     * Filter by size.
     *
     * @param string $size
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function size($size)
    {
        return new self(iterator_to_array(
            new SizeRangeFilterIterator($this->getIterator(), [new NumberComparator($size)])
        ));
    }

    /**
     * Filter by date.
     *
     * @param string $date
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function date($date)
    {
        return new self(iterator_to_array(
            new DateRangeFilterIterator($this->getIterator(), [new DateComparator($date)])
        ));
    }

    /**
     * Filter by closure.
     *
     * @param \Closure $closure
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function filterByClosure(Closure $closure)
    {
        return new self(iterator_to_array(
            new CustomFilterIterator($this->getIterator(), [$closure])
        ));
    }

    /**
     * Get iterator.
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return parent::getIterator();
    }

    /**
     * Filter by file list.
     *
     * @param \Traversable $fileList
     *
     * @return \ClickNow\Checker\Util\FilesCollection
     */
    public function filterByFileList(Traversable $fileList)
    {
        $allowedFiles = array_map(function (SplFileInfo $file) {
            return $file->getPathname();
        }, iterator_to_array($fileList));

        return $this->filter(function (SplFileInfo $file) use ($allowedFiles) {
            return in_array($file->getPathname(), $allowedFiles);
        });
    }

    /**
     * Get all paths.
     *
     * @return array
     */
    public function getAllPaths()
    {
        return $this->map(function (SplFileInfo $file) {
            return $file->getPathname();
        })->toArray();
    }
}
