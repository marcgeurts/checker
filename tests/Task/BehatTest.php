<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Behat
 */
class BehatTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('behat', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('config', $options);
        $this->assertContains('format', $options);
        $this->assertContains('suite', $options);
        $this->assertContains('finder', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Behat|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Behat::class, null, [
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('behat')->once()->andReturn($args);
    }
}
