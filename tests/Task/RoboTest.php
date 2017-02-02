<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Robo
 * @covers \ClickNow\Checker\Task\AbstractExternalTask
 */
class RoboTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('robo', $this->externalTask->getName());
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Robo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Robo::class, null, [
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
