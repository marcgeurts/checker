<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Grunt
 */
class GruntTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('grunt', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('gruntfile', $options);
        $this->assertContains('task', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Grunt|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Grunt::class, null, [
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
