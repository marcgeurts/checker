<?php

namespace ClickNow\Checker\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ExtensionInterface
{
    /**
     * Load extension.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function load(ContainerBuilder $container);
}
