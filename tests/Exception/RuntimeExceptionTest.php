<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\RuntimeException
 */
class RuntimeExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\RuntimeException
     */
    protected $runtimeException;

    public function setUp()
    {
        $this->runtimeException = new RuntimeException();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->runtimeException);
        $this->assertInstanceOf(ExceptionInterface::class, $this->runtimeException);
    }
}
