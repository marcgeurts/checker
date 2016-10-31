<?php

use ClickNow\Checker\Command\Command;
use ClickNow\Checker\Result\Result;
use Mockery as m;

class AbstractCommandRunnerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\Command
     */
    protected $command;

    protected function setUp()
    {
        $checker = m::mock('ClickNow\Checker\Config\Checker');
        $checker->shouldReceive('getProcessTimeout')->andReturn(null);
        $checker->shouldReceive('shouldStopOnFailure')->andReturn(false);
        $checker->shouldReceive('shouldIgnoreUnstagedChanges')->andReturn(false);
        $checker->shouldReceive('isSkipSuccessOutput')->andReturn(false);
        $checker->shouldReceive('getMessage')->andReturn(null);

        $this->command = new Command($checker, 'foo');
    }

    public function testRun()
    {
        $context = m::mock('ClickNow\Checker\Context\ContextInterface');

        $action1 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action1->shouldReceive('getName')->andReturn('action1');
        $action1->shouldReceive('canRunInContext')->once()->andReturn(true);
        $action1->shouldReceive('run')->once()->andReturn(Result::success($this->command, $context, $action1))->byDefault();
        $this->command->addAction($action1);

        $action2 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action2->shouldReceive('getName')->andReturn('action2');
        $action2->shouldReceive('canRunInContext')->once()->andReturn(true);
        $action2->shouldReceive('run')->once()->andReturn(Result::success($this->command, $context, $action2))->byDefault();
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }
}