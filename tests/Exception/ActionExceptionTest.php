<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\ActionException
 */
class ActionExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ActionException
     */
    protected $actionException;

    public function setUp()
    {
        $this->actionException = new ActionException('action');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->actionException);
    }

    public function testGetMessage()
    {
        $this->assertEmpty($this->actionException->getMessage());
    }

    public function testGetActionName()
    {
        $this->assertSame('action', $this->actionException->getActionName());
    }
}
