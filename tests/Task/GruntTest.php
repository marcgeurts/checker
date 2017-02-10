<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Grunt
 */
class GruntTest extends AbstractExternalTaskTest
{
    protected $class = Grunt::class;

    public function testGetName()
    {
        $this->assertSame('Grunt', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('grunt')->once()->andReturn($args);
    }
}
