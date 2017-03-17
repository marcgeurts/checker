<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\TsLint
 */
class TsLintTest extends AbstractExternalTaskTest
{
    protected $class = TsLint::class;

    public function testGetName()
    {
        $this->assertSame('TSLint', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('tslint')->once()->andReturn($args);
    }
}
