<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\ExtensionAlreadyRegisteredException
 */
class ExtensionAlreadyRegisteredExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ExtensionAlreadyRegisteredException
     */
    protected $extensionAlreadyRegisteredException;

    public function setUp()
    {
        $this->extensionAlreadyRegisteredException = new ExtensionAlreadyRegisteredException('extension');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ExtensionException::class, $this->extensionAlreadyRegisteredException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Extension `extension` already registered.',
            $this->extensionAlreadyRegisteredException->getMessage()
        );
    }
}
