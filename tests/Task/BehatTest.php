<?php

namespace ClickNow\Checker\Task;

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
     * Get external task command name.
     *
     * @return string
     */
    protected function getExternalTaskCommandName()
    {
        return $this->externalTask->getName();
    }
}
