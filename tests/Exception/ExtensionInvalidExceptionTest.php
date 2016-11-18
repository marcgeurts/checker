<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\ExtensionInvalidException
 */
class ExtensionInvalidExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ExtensionInvalidException
     */
    protected $extensionInvalidException;

    public function setUp()
    {
        $this->extensionInvalidException = new ExtensionInvalidException('extension');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ExtensionException::class, $this->extensionInvalidException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Extension `extension` must implement ExtensionInterface.',
            $this->extensionInvalidException->getMessage()
        );
    }
}
