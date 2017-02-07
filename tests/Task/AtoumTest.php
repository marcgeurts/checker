<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Atoum
 */
class AtoumTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('atoum', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('configuration', $options);
        $this->assertContains('bootstrap-file', $options);
        $this->assertContains('namespaces', $options);
        $this->assertContains('methods', $options);
        $this->assertContains('tags', $options);
        $this->assertContains('finder', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Atoum|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Atoum::class, null, [
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('atoum')->once()->andReturn($args);
    }
}
