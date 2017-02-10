<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\DoctrineOrm
 */
class DoctrineOrmTest extends AbstractExternalTaskTest
{
    protected $class = DoctrineOrm::class;

    public function testGetName()
    {
        $this->assertSame('Doctrine ORM', $this->externalTask->getName());
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
        $this->processBuilder->shouldReceive('createArgumentsForCommand')->with('doctrine')->once()->andReturn($args);
    }
}
