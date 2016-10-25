<?php

namespace ClickNow\Checker\Console;

use Composer\Package\PackageInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;

class Config
{
    const CONFIG_FILE = 'checker.yml';

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
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
     * Config.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \Composer\Package\PackageInterface|null  $package
     */
    public function __construct(Filesystem $filesystem, PackageInterface $package = null)
    {
        $this->filesystem = $filesystem;
        $this->package = $package;
        $this->defaultPath = $this->initializeDefaultPath();
    }

    /**
     * Get input option.
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    public function getInputOption()
    {
        return new InputOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Path to config',
            $this->defaultPath
        );
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        $input = new ArgvInput();
        $option = $input->getParameterOption(['--config', '-c']);

        return $option ?: $this->defaultPath;
    }

    /**
     * Get default path.
     *
     * @return string
     */
    public function getDefaultPath()
    {
        return $this->defaultPath;
    }

    /**
     * Initialize default path.
     *
     * @return string
     */
    private function initializeDefaultPath()
    {
        $defaultPath = getcwd().DIRECTORY_SEPARATOR.self::CONFIG_FILE;

        // use path from composer
        if (!is_null($this->package)) {
            $extra = $this->package->getExtra();

            if (isset($extra['checker']['config'])) {
                $defaultPath = $extra['checker']['config'];
            }
        }

        // use path with dist
        $distPath = (substr($defaultPath, -5) !== '.dist') ? $defaultPath.'.dist' : $defaultPath;
        if ($this->filesystem->exists($distPath)) {
            $defaultPath = $distPath;
        }

        // Make sure to set the full path when it is declared relative
        // This will fix some issues in windows.
        if (!$this->filesystem->isAbsolutePath($defaultPath)) {
            $defaultPath = getcwd().DIRECTORY_SEPARATOR.$defaultPath;
        }

        return $defaultPath;
    }
}
