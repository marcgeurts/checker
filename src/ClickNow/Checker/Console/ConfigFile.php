<?php

namespace ClickNow\Checker\Console;

use ClickNow\Checker\Repository\Filesystem;
use Composer\Package\PackageInterface;

class ConfigFile
{
    /**
     * @var \ClickNow\Checker\Repository\Filesystem
     */
    private $filesystem;

    /**
     * @var \Composer\Package\PackageInterface
     */
    private $package;

    /**
     * @var string
     */
    private $defaultPath;

    /**
     * Config file.
     *
     * @param \ClickNow\Checker\Repository\Filesystem $filesystem
     * @param \Composer\Package\PackageInterface|null $package
     */
    public function __construct(Filesystem $filesystem, PackageInterface $package = null)
    {
        $this->filesystem = $filesystem;
        $this->package = $package;
    }

    /**
     * Get default path.
     *
     * @return string
     */
    public function getDefaultPath()
    {
        if (!$this->defaultPath) {
            $defaultPath = getcwd().DIRECTORY_SEPARATOR.'checker.yml';

            if (!is_null($this->package)) {
                $defaultPath = $this->useConfigPathFromComposer($this->package, $defaultPath);
            }

            $defaultPath = $this->useConfigPathWithDistSupport($defaultPath);

            if (!$this->filesystem->isAbsolutePath($defaultPath)) {
                $defaultPath = getcwd().DIRECTORY_SEPARATOR.$defaultPath;
            }

            $this->defaultPath = $defaultPath;
        }

        return $this->defaultPath;
    }

    /**
     * Use config path from composer.
     *
     * @param \Composer\Package\PackageInterface $package
     * @param string                             $defaultPath
     *
     * @return string
     */
    private function useConfigPathFromComposer(PackageInterface $package, $defaultPath)
    {
        $extra = $package->getExtra();

        if (isset($extra['checker']['config'])) {
            return (string) $extra['checker']['config'];
        }

        return $defaultPath;
    }

    /**
     * Use config path with dist support.
     *
     * @param string $defaultPath
     *
     * @return string
     */
    private function useConfigPathWithDistSupport($defaultPath)
    {
        $distPath = (substr($defaultPath, -5) !== '.dist') ? $defaultPath.'.dist' : $defaultPath;
        if ($this->filesystem->exists($distPath)) {
            return $distPath;
        }

        return $defaultPath;
    }
}
