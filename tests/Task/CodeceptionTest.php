<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Codeception
 */
class CodeceptionTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('codeception', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('override', $options);
        $this->assertContains('config', $options);
        $this->assertContains('report', $options);
        $this->assertContains('silent', $options);
        $this->assertContains('steps', $options);
        $this->assertContains('debug', $options);
        $this->assertContains('group', $options);
        $this->assertContains('skip', $options);
        $this->assertContains('skip-group', $options);
        $this->assertContains('env', $options);
        $this->assertContains('fail-fast', $options);
        $this->assertContains('suite', $options);
        $this->assertContains('test', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Codeception|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Codeception::class, null, [
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('codecept')->once()->andReturn($args);
    }
}
