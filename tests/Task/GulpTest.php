<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Gulp
 * @covers \ClickNow\Checker\Task\AbstractExternalTask
 */
class GulpTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('gulp', $this->externalTask->getName());
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Gulp|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Gulp::class, null, [
            $this->processBuilder,
            $this->processFormatter,
        ]);
    }

    /**
     * Get external task name.
     *
     * @return string
     */
    protected function getExternalTaskName()
    {
        return $this->externalTask->getName();
    }
}
