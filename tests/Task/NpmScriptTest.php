<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\NpmScript
 */
class NpmScriptTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('npm-script', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('is-run-task', $options);
        $this->assertContains('script', $options);
        $this->assertContains('working-directory', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\NpmScript|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(NpmScript::class, null, [
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('npm')->once()->andReturn($args);
    }

    /**
     * Get action config.
     *
     * @return array
     */
    protected function getActionConfig()
    {
        return array_merge(['script' => 'foo'], parent::getActionConfig());
    }
}
