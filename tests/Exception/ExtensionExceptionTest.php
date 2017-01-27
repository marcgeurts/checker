<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\ExtensionException
 */
class ExtensionExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ExtensionException
     */
    protected $extensionException;

    public function setUp()
    {
        $this->extensionException = new ExtensionException('extension');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->extensionException);
    }

    public function testGetMessage()
    {
        $this->assertEmpty($this->extensionException->getMessage());
    }

    public function testGetExtensionClass()
    {
        $this->assertSame('extension', $this->extensionException->getExtensionClass());
    }
}
