<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\ExtensionNotFoundException
 */
class ExtensionNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ExtensionNotFoundException
     */
    protected $extensionNotFoundException;

    public function setUp()
    {
        $this->extensionNotFoundException = new ExtensionNotFoundException('extension');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ExtensionException::class, $this->extensionNotFoundException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Extension `extension` was not found.',
            $this->extensionNotFoundException->getMessage()
        );
    }
}
