<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\SecurityChecker
 */
class SecurityCheckerTest extends AbstractExternalTaskTest
{
    protected $class = SecurityChecker::class;

    public function testGetName()
    {
        $this->assertSame('Security Checker', $this->externalTask->getName());
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
            ->with('security-checker')
            ->once()
            ->andReturn($args);
    }
}
