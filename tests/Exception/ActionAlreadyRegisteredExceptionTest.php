<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\ActionAlreadyRegisteredException
 */
class ActionAlreadyRegisteredExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ActionAlreadyRegisteredException
     */
    protected $actionAlreadyRegisteredException;

    public function setUp()
    {
        $this->actionAlreadyRegisteredException = new ActionAlreadyRegisteredException('action');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ActionException::class, $this->actionAlreadyRegisteredException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Action `action` already registered.',
            $this->actionAlreadyRegisteredException->getMessage()
        );
    }
}
