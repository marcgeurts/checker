<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Make
 */
class MakeTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('make', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('makefile', $options);
        $this->assertContains('task', $options);
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
