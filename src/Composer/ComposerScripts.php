<?php

namespace ClickNow\Checker\Composer;

use ClickNow\Checker\Console\Application;
use Composer\Script\Event;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * @codeCoverageIgnore
 */
class ComposerScripts
{
    /**
     * Handle the post-update Composer event.
     *
     * @param \Composer\Script\Event $event
     *
     * @return void
     */
    public static function postInstall(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::install();
    }

    /**
     * Handle the post-update Composer event.
     *
     * @param \Composer\Script\Event $event
     *
     * @return void
     */
    public static function postUpdate(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::install();
    }

    /**
     * Install.
     *
     * @return void
     */
    private static function install()
    {
        $input = new ArrayInput(['command' => 'git:install']);

        $application = new Application();
        $application->run($input);
    }
}
