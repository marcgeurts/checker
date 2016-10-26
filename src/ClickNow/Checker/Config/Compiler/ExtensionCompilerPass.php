<?php

namespace ClickNow\Checker\Config\Compiler;

use ClickNow\Checker\Exception\ExtensionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ExtensionInvalidException;
use ClickNow\Checker\Exception\ExtensionNotFoundException;
use ClickNow\Checker\Extension\ExtensionInterface;

class ExtensionCompilerPass extends AbstractCompilerPass
{
    private $extensionsRegistered = [];

    /**
     * Configure extensions to run.
     *
     * @throws \ClickNow\Checker\Exception\ExtensionNotFoundException
     * @throws \ClickNow\Checker\Exception\ExtensionAlreadyRegisteredException
     * @throws \ClickNow\Checker\Exception\ExtensionInvalidException
     *
     * @return void
     */
    protected function run()
    {
        $extensions = (array) $this->container->getParameter('extensions');

        foreach ($extensions as $extensionClass) {
            $this->loadExtensionClass($extensionClass);
        }
    }

    /**
     * Load extension class.
     *
     * @param string $extensionClass
     *
     * @return void
     */
    private function loadExtensionClass($extensionClass)
    {
        // Checks if the class exists
        if (!class_exists($extensionClass)) {
            throw new ExtensionNotFoundException($extensionClass);
        }

        // Instance extension class
        $extension = new $extensionClass();
        $name = get_class($extension);

        // Checks if the class has already been registered
        if (array_key_exists($name, $this->extensionsRegistered)) {
            throw new ExtensionAlreadyRegisteredException($extensionClass);
        }

        // Checks if the class implements the ExtensionInterface
        if (!$extension instanceof ExtensionInterface) {
            throw new ExtensionInvalidException($extensionClass);
        }

        $this->extensionsRegistered[$name] = $extension;
        $extension->load($this->container);
    }
}
