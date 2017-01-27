<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\PlatformException
 */
class PlatformExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\PlatformException
     */
    protected $platformException;

    public function setUp()
    {
        $this->platformException = new PlatformException();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ExceptionInterface::class, $this->platformException);
    }
}
