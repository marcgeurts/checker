<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Brunch
 */
class BrunchTest extends AbstractExternalTaskTest
{
    protected $class = Brunch::class;

    public function testGetName()
    {
        $this->assertSame('Brunch', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('brunch')->once()->andReturn($args);
    }
}
