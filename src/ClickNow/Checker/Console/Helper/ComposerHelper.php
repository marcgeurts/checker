<?php

namespace ClickNow\Checker\Console\Helper;

use Composer\Config;
use Composer\Package\PackageInterface;
use Symfony\Component\Console\Helper\Helper;

class ComposerHelper extends Helper
{
    const HELPER_NAME = 'composer';

    /**
     * @var \Composer\Config|null
     */
    private $config;

    /**
     * @var \Composer\Package\PackageInterface|null
     */
    private $package;

    /**
     * Composer helper.
     *
     * @param \Composer\Config|null                   $config
     * @param \Composer\Package\PackageInterface|null $package
     */
    public function __construct(Config $config = null, PackageInterface $package = null)
    {
        $this->config = $config;
        $this->package = $package;
    }

    /**
     * Get config.
     *
     * @return \Composer\Config|null
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Has config?
     *
     * @return bool
     */
    public function hasConfig()
    {
        return $this->config !== null;
    }

    /**
     * Get package.
     *
     * @return \Composer\Package\PackageInterface|null
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Has package?
     *
     * @return bool
     */
    public function hasPackage()
    {
        return $this->package !== null;
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
