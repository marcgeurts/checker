<?php

namespace ClickNow\Checker\Composer;

use ClickNow\Checker\Console\Application;
use Composer\Script\Event;
use Symfony\Component\Console\Input\ArrayInput;

class ComposerScripts
{
    public static function postInstall(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        $input = new ArrayInput(['command' => 'git:install']);

        $application = new Application();
        $application->run($input);
    }

    public static function postUpdate(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        $input = new ArrayInput(['command' => 'git:install']);

        $application = new Application();
        $application->run($input);
    }
}
