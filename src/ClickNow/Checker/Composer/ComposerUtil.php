<?php

namespace ClickNow\Checker\Composer;

use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\Loader\JsonLoader;
use Composer\Package\Loader\RootPackageLoader;
use Composer\Repository\RepositoryFactory;
use Exception;

class ComposerUtil
{
    /**
     * Load package.
     *
     * @return \Composer\Package\PackageInterface|null
     */
    public static function loadPackage()
    {
        try {
            $config = Factory::createConfig();
            self::ensureProjectBinDirInSystemPath($config->get('bin-dir'));
            $loader = new JsonLoader(new RootPackageLoader(RepositoryFactory::manager(new NullIO(), $config), $config));
            $package = $loader->load(getcwd().DIRECTORY_SEPARATOR.'composer.json');
        } catch (Exception $e) {
            $package = null;
        }

        return $package;
    }

    /**
     * Composer contains some logic to prepend the current bin dir to the system PATH.
     * To make sure this application works the same in CLI and Composer modus,
     * we'll have to ensure that the bin path is always prefixed.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     *
     * @param string $binDir
     *
     * @return void
     */
    private static function ensureProjectBinDirInSystemPath($binDir)
    {
        $absoluteBinDir = realpath($binDir);
        $pathStr = self::getPathStr();
        $match = preg_match(
            '{(^|'.PATH_SEPARATOR.')'.preg_quote($absoluteBinDir).'($|'.PATH_SEPARATOR.')}',
            $_SERVER[$pathStr]
        );

        if (!is_dir($absoluteBinDir) || !isset($_SERVER[$pathStr]) || $match) {
            return;
        }

        $_SERVER[$pathStr] = $absoluteBinDir.PATH_SEPARATOR.getenv($pathStr);
        putenv($pathStr.'='.$_SERVER[$pathStr]);
    }

    /**
     * Get path str.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     *
     * @return string
     */
    private static function getPathStr()
    {
        return (!isset($_SERVER['PATH']) && isset($_SERVER['Path'])) ? 'Path' : 'PATH';
    }
}
