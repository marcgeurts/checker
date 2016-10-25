<?php

namespace ClickNow\Checker\Composer;

use ClickNow\Checker\Exception\RuntimeException;
use Composer\Config;
use Composer\Factory;
use Composer\IO\NullIO;
use Composer\Package\Loader\JsonLoader;
use Composer\Package\Loader\RootPackageLoader;
use Composer\Repository\RepositoryFactory;
use Exception;

class ComposerUtil
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
            return Factory::createConfig();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    /**
     * Load composer package.
     *
     * @param \Composer\Config $config
     *
     * @throws \ClickNow\Checker\Exception\RuntimeException
     *
     * @return \Composer\Package\PackageInterface
     */
    public static function loadPackage(Config $config)
    {
        try {
            $loader = new JsonLoader(new RootPackageLoader(
                RepositoryFactory::manager(new NullIO(), $config),
                $config
            ));

            return $loader->load(getcwd().DIRECTORY_SEPARATOR.'composer.json');
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
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
        $absoluteBinDir = realpath($binDir);
        $pathStr = (!isset($_SERVER['PATH']) && isset($_SERVER['Path'])) ? 'Path' : 'PATH';
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
}
