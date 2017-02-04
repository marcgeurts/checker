<?php

namespace ClickNow\Checker\Repository;

use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem as SymfonFilesystem;

class Filesystem extends SymfonFilesystem
{
    /**
     * Read from file info
     *
     * @param SplFileInfo $file
     *
     * @return string
     */
    public function readFromFileInfo(SplFileInfo $file)
    {
        $handle = $file->openFile('r');
        $content = '';
        while (!$handle->eof()) {
            $content .= $handle->fgets();
        }

        return $content;
    }
}
