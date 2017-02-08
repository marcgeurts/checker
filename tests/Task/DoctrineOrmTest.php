<?php

namespace ClickNow\Checker\Task;

use ClickNow\Checker\Process\ArgumentsCollection;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\DoctrineOrm
 */
class DoctrineOrmTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('doctrine-orm', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('skip-mapping', $options);
        $this->assertContains('skip-sync', $options);
        $this->assertContains('finder', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\DoctrineOrm|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(DoctrineOrm::class, null, [
            $this->processBuilder,
            $this->processFormatter,
        ]);
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
