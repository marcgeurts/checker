<?php

namespace ClickNow\Checker\Util;

use ClickNow\Checker\Exception\RuntimeException;
use Composer\Config;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\Loader\JsonLoader;
use Composer\Package\Loader\RootPackageLoader;
use Composer\Repository\RepositoryFactory;
use Exception;

class Composer
{
    /**
     * Load composer config.
     *
     * @throws \ClickNow\Checker\Exception\RuntimeException
     *
     * @return \Composer\Config
     */
    public static function loadConfig()
    {
        try {
            $config = Factory::createConfig();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $config;
    }

    /**
     * Load composer root package.
     *
     * @param \Composer\Config $config
     *
     * @throws \ClickNow\Checker\Exception\RuntimeException
     *
     * @return \Composer\Package\RootPackageInterface
     */
    public static function loadRootPackage(Config $config)
    {
        try {
            $loader = new JsonLoader(new RootPackageLoader(
                RepositoryFactory::manager(new NullIO(), $config),
                $config
            ));
            $package = $loader->load(getcwd().DIRECTORY_SEPARATOR.'composer.json');
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $package;
    }

    /**
     * Composer contains some logic to prepend the current bin dir to the system PATH.
     * To make sure this application works the same in CLI and Composer modus,
     * we'll have to ensure that the bin path is always prefixed.
     *
     * @param string $binDir
     *
     * @return void
     */
    public static function ensureProjectBinDirInSystemPath($binDir)
    {
        $binDir = realpath($binDir);
        $pathStr = (!isset($_SERVER['PATH']) && isset($_SERVER['Path'])) ? 'Path' : 'PATH';

        if (!is_dir($binDir) ||
            !isset($_SERVER[$pathStr]) ||
            preg_match('{(^|'.PATH_SEPARATOR.')'.preg_quote($binDir).'($|'.PATH_SEPARATOR.')}', $_SERVER[$pathStr])
        ) {
            return;
        }

        $_SERVER[$pathStr] = $binDir.PATH_SEPARATOR.getenv($pathStr);
        putenv($pathStr.'='.$_SERVER[$pathStr]);
    }
}
