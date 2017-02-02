<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Make
 * @covers \ClickNow\Checker\Task\AbstractExternalTask
 */
class MakeTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('make', $this->externalTask->getName());
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Make|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Make::class, null, [
            $this->processBuilder,
            $this->processFormatter,
        ]);
    }

    /**
     * Get external task command name.
     *
     * @return string
     */
    protected function getExternalTaskCommandName()
    {
        return $this->externalTask->getName();
    }
}
