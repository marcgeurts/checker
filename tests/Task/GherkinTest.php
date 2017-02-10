<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Gherkin
 */
class GherkinTest extends AbstractExternalTaskTest
{
    protected $class = Gherkin::class;

    public function testGetName()
    {
        $this->assertSame('Gherkin', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('kawaii')->once()->andReturn($args);
    }
}
