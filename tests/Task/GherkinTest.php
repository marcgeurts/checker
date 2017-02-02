<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Gherkin
 */
class GherkinTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('gherkin', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('align', $options);
        $this->assertContains('directory', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Gherkin|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Gherkin::class, null, [
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
        return 'kawaii';
    }
}
