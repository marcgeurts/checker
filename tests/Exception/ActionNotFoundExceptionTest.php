<?php

namespace ClickNow\Checker\Exception;

/**
 * @group  exception
 * @covers \ClickNow\Checker\Exception\ActionNotFoundException
 */
class ActionNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\ActionNotFoundException
     */
    protected $actionNotFoundException;

    public function setUp()
    {
        $this->actionNotFoundException = new ActionNotFoundException('action');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ActionException::class, $this->actionNotFoundException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Action `action` was not found.',
            $this->actionNotFoundException->getMessage()
        );
    }
}
