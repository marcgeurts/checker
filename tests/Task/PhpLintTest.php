<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\PhpLint
 */
class PhpLintTest extends AbstractExternalTaskTest
{
    protected $class = PhpLint::class;

    public function testGetName()
    {
        $this->assertSame('PHPLint', $this->externalTask->getName());
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
        $this->processBuilder
            ->shouldReceive('createArgumentsForCommand')
            ->with('parallel-lint')
            ->once()
            ->andReturn($args);
    }
}
