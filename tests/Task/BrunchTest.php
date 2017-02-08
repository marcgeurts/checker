<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Brunch
 */
class BrunchTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('brunch', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('task', $options);
        $this->assertContains('env', $options);
        $this->assertContains('jobs', $options);
        $this->assertContains('debug', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Brunch|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Brunch::class, null, [
            $this->processBuilder,
            $this->processFormatter,
        ]);
    }

    /**
     * Mock arguments
     *
     * @param \ClickNow\Checker\Process\ArgumentsCollection $args
     *
     * @return void
     */
    protected function mockArguments(ArgumentsCollection $args)
    {
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('brunch')->once()->andReturn($args);
    }
}
