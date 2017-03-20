<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\SassLint
 */
class SassLintTest extends AbstractExternalTaskTest
{
    protected $class = SassLint::class;

    public function testGetName()
    {
        $this->assertSame('SassLint', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('sass-lint')->once()->andReturn($args);
    }
}
