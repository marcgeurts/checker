<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\PhpCpd
 */
class PhpCpdTest extends AbstractExternalTaskTest
{
    protected $class = PhpCpd::class;

    public function testGetName()
    {
        $this->assertSame('PHP Copy/Paste Detector (phpcpd)', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('phpcpd')->once()->andReturn($args);
    }
}
