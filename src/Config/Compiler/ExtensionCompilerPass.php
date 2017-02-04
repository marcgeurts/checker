<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\ExtensionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ExtensionInvalidException;
use ClickNow\Checker\Exception\ExtensionNotFoundException;
use ClickNow\Checker\Extension\ExtensionInterface;

class ExtensionCompilerPass extends AbstractCompilerPass
{
    /**
     * @var array
     */
    private $registered = [];

    /**
     * Configure.
     *
     * @throws \ClickNow\Checker\Exception\ExtensionNotFoundException
     *
     * @return void
     */
    protected function configure()
    {
        $extensions = (array) $this->container->getParameter('extensions');

        foreach ($extensions as $extension) {
            if (!class_exists($extension)) {
                throw new ExtensionNotFoundException($extension);
            }

            $this->loadExtension(new $extension());
        }
    }

    /**
     * Load extension.
     *
     * @param object $extension
     *
     * @throws \ClickNow\Checker\Exception\ExtensionAlreadyRegisteredException
     * @throws \ClickNow\Checker\Exception\ExtensionInvalidException
     *
     * @return void
     */
    private function loadExtension($extension)
    {
        $name = get_class($extension);

        if (array_key_exists($name, $this->registered)) {
            throw new ExtensionAlreadyRegisteredException($name);
        }

        if (!$extension instanceof ExtensionInterface) {
            throw new ExtensionInvalidException($name);
        }

        $this->registered[$name] = $extension;
        $extension->load($this->container);
    }
}
