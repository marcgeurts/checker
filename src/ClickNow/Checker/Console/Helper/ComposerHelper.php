<?php

namespace ClickNow\Checker\Console\Helper;

use Composer\Config;
use Composer\Package\RootPackageInterface;
use Symfony\Component\Console\Helper\Helper;

class ComposerHelper extends Helper
{
    const HELPER_NAME = 'composer';

    /**
     * @var \Composer\Config|null
     */
    private $config;

    /**
     * @var \Composer\Package\RootPackageInterface|null
     */
    private $rootPackage;

    /**
     * Composer helper.
     *
     * @param \Composer\Config|null                       $config
     * @param \Composer\Package\RootPackageInterface|null $rootPackage
     */
    public function __construct(Config $config = null, RootPackageInterface $rootPackage = null)
    {
        $this->config = $config;
        $this->rootPackage = $rootPackage;
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
     * Get root package.
     *
     * @return \Composer\Package\RootPackageInterface|null
     */
    public function getRootPackage()
    {
        return $this->rootPackage;
    }

    /**
     * Has root package?
     *
     * @return bool
     */
    public function hasRootPackage()
    {
        return $this->rootPackage !== null;
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
