<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\NpmScript
 */
class NpmScriptTest extends AbstractExternalTaskTest
{
    protected $class = NpmScript::class;

    public function testGetName()
    {
        $this->assertSame('NPM script', $this->externalTask->getName());
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
