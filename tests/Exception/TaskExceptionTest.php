<?php

namespace ClickNow\Checker\Exception;

/**
 * @group exception
 * @covers \ClickNow\Checker\Exception\TaskException
 */
class TaskExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Exception\TaskException
     */
    protected $taskException;

    public function setUp()
    {
        $this->taskException = new TaskException('task');
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RuntimeException::class, $this->taskException);
    }

    public function testGetMessage()
    {
        $this->assertEmpty($this->taskException->getMessage());
    }

    public function testGetTaskName()
    {
        $this->assertSame('task', $this->taskException->getTaskName());
    }
}
