<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Gulp
 */
class GulpTest extends AbstractExternalTaskTest
{
    protected $class = Gulp::class;

    public function testGetName()
    {
        $this->assertSame('Gulp', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('gulp')->once()->andReturn($args);
    }
}
