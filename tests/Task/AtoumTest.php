<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Atoum
 */
class AtoumTest extends AbstractExternalTaskTest
{
    protected $class = Atoum::class;

    public function testGetName()
    {
        $this->assertSame('Atoum', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('atoum')->once()->andReturn($args);
    }
}
