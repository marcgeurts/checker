<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\StyleLint
 */
class StyleLintTest extends AbstractExternalTaskTest
{
    protected $class = StyleLint::class;

    public function testGetName()
    {
        $this->assertSame('StyleLint', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('stylelint')->once()->andReturn($args);
    }
}
