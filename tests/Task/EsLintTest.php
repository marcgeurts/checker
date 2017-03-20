<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\EsLint
 */
class EsLintTest extends AbstractExternalTaskTest
{
    protected $class = EsLint::class;

    public function testGetName()
    {
        $this->assertSame('ESLint', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('eslint')->once()->andReturn($args);
    }
}
