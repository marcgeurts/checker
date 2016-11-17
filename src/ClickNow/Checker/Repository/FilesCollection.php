<?php

namespace ClickNow\Checker\Repository;

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
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function filterByName($pattern)
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
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function filterByNotName($pattern)
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
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function filterByPath($pattern)
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
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function filterByNotPath($pattern)
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
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function filterByExtensions(array $extensions)
    {
        if (count($extensions) < 1) {
            return new self();
        }

        return $this->filterByName(sprintf('/\.(%s)$/i', implode('|', $extensions)));
    }

    /**
     * Filter by size.
     *
     * @param string $size
     *
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function filterBySize($size)
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
     * @return \ClickNow\Checker\Repository\FilesCollection
     */
    public function filterByDate($date)
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
     * @return \ClickNow\Checker\Repository\FilesCollection
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
     * @return \ClickNow\Checker\Repository\FilesCollection
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
