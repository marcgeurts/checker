<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\PhpMd
 */
class PhpMdTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('phpmd', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('ruleset', $options);
        $this->assertContains('minimum-priority', $options);
        $this->assertContains('strict', $options);
        $this->assertContains('finder', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\PhpMd|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(PhpMd::class, null, [
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('phpmd')->once()->andReturn($args);
    }
}
