<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\PhpMd
 */
class PhpMdTest extends AbstractExternalTaskTest
{
    protected $class = PhpMd::class;

    public function testGetName()
    {
        $this->assertSame('PHP Mess Detector (phpmd)', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('phpmd')->once()->andReturn($args);
    }
}
