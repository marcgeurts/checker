<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\TaskNotFoundException
 */
class TaskNotFoundExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\TaskNotFoundException
     */
    protected $taskNotFoundException;

    public function setUp()
    {
        $this->taskNotFoundException = new TaskNotFoundException('task');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(TaskException::class, $this->taskNotFoundException);
    }

    public function testGetMessage()
    {
        $this->assertSame(
            'Task `task` was not found.',
            $this->taskNotFoundException->getMessage()
        );
    }
}
