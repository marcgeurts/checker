<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\InvalidArgumentException
 */
class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\InvalidArgumentException
     */
    protected $invalidArgumentException;

    public function setUp()
    {
        $this->invalidArgumentException = new InvalidArgumentException();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->invalidArgumentException);
    }
}
