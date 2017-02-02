<?php

namespace ClickNow\Checker\Task;

/**
 * @group  task
 * @covers \ClickNow\Checker\Task\Gulp
 */
class GulpTest extends AbstractExternalTaskTest
{
    public function testGetName()
    {
        $this->assertSame('gulp', $this->externalTask->getName());
    }

    public function testConfigOptions()
    {
        $options = $this->externalTask->getConfigOptions()->getDefinedOptions();

        $this->assertContains('gulpfile', $options);
        $this->assertContains('task', $options);
    }

    /**
     * Mock external task.
     *
     * @return \ClickNow\Checker\Task\Gulp|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExternalTask()
    {
        return $this->getMock(Gulp::class, null, [
            $this->processBuilder,
            $this->processFormatter,
        ]);
    }

    /**
     * Get external task command name.
     *
     * @return string
     */
    protected function getExternalTaskCommandName()
    {
        return $this->externalTask->getName();
    }
}
