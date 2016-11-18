<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\TaskAlreadyRegisteredException
 */
class TaskAlreadyRegisteredExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\TaskAlreadyRegisteredException
     */
    protected $taskAlreadyRegisteredException;

    public function setUp()
    {
        $this->taskAlreadyRegisteredException = new TaskAlreadyRegisteredException('task');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(TaskException::class, $this->taskAlreadyRegisteredException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Task `task` already registered.',
            $this->taskAlreadyRegisteredException->getMessage()
        );
    }
}
