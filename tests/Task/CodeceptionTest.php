<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Codeception
 */
class CodeceptionTest extends AbstractExternalTaskTest
{
    protected $class = Codeception::class;

    public function testGetName()
    {
        $this->assertSame('Codeception', $this->externalTask->getName());
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
