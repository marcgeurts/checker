<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Ant
 * @covers \ClickNow\Checker\Task\AbstractExternalTask
 */
class AntTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('ant', $this->externalTask->getName());
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Ant|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Ant::class, null, [
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
