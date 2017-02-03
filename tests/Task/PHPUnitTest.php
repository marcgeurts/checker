<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\PHPUnit
 */
class PHPUnitTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('phpunit', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('configuration', $options);
        $this->assertContains('group', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\PHPUnit|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(PHPUnit::class, null, [
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('phpunit')->once()->andReturn($args);
    }
}
