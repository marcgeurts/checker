<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\ActionInvalidResultException
 */
class ActionInvalidResultExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ActionInvalidResultException
     */
    protected $actionInvalidResultException;

    public function setUp()
    {
        $this->actionInvalidResultException = new ActionInvalidResultException('action');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ActionException::class, $this->actionInvalidResultException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Action `action` did not return a Result.',
            $this->actionInvalidResultException->getMessage()
        );
    }
}
