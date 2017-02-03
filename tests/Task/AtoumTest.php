<?php

namespace ClickNow\Checker\Task;

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
        $this->assertContains('directories', $options);
        $this->assertContains('files', $options);
        $this->assertContains('namespaces', $options);
        $this->assertContains('methods', $options);
        $this->assertContains('tags', $options);
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
     * Get external task command name.
     *
     * @return string
     */
    protected function getExternalTaskCommandName()
    {
        return $this->externalTask->getName();
    }
}
