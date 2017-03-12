<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Php7cc
 */
class Php7ccTest extends AbstractExternalTaskTest
{
    protected $class = Php7cc::class;

    public function testGetName()
    {
        $this->assertSame('PHP 7 Compatibility Checker(php7cc)', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('php7cc')->once()->andReturn($args);
    }
}
