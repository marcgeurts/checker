<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\PhpUnit
 */
class PhpUnitTest extends AbstractExternalTaskTest
{
    protected $class = PhpUnit::class;

    public function testGetName()
    {
        $this->assertSame('PHPUnit', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('phpunit')->once()->andReturn($args);
    }
}
