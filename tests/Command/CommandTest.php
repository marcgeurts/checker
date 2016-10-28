<?php

use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Command\Command;
use Mockery as m;

class CommandTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\Command
     */
    protected $command;

    /**
     * @var \Mockery\MockInterface
     */
    protected $action;

    protected function setUp()
    {
        $checker = m::mock('ClickNow\Checker\Config\Checker');
        $checker->shouldReceive('getProcessTimeout')->andReturn(30);
        $checker->shouldReceive('shouldStopOnFailure')->andReturn(true);
        $checker->shouldReceive('shouldIgnoreUnstagedChanges')->andReturn(true);
        $checker->shouldReceive('isSkipSuccessOutput')->andReturn(true);
        $checker->shouldReceive('getMessage')->andReturn('bar');

        $this->command = new Command($checker, 'foo');

        $this->action = m::mock('ClickNow\Checker\Action\ActionInterface');
        $this->action->shouldReceive('getName')->andReturn('bar');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testNameOfCommand()
    {
        $this->assertEquals('foo', $this->command->getName());
    }

    public function testAddAction()
    {
        $this->assertEmpty($this->command->getActions());
        $this->command->addAction($this->action, []);

        $actions = $this->command->getActions();
        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertCount(1, $actions);
        $this->assertSame($this->action, $actions->first());
    }

    /**
     * @expectedException \ClickNow\Checker\Exception\ActionAlreadyRegisteredException
     * @expectedExceptionMessage Action `bar` already registered.
     */
    public function testAddActionThrowsWhenActionHasAlreadyBeenAdded()
    {
        $this->command->addAction($this->action, []);
        $this->command->addAction($this->action, []);
    }

    public function testDefaultConfig()
    {
        $this->assertEquals(30, $this->command->getProcessTimeout());
        $this->assertTrue($this->command->shouldStopOnFailure());
        $this->assertTrue($this->command->shouldIgnoreUnstagedChanges());
        $this->assertTrue($this->command->isSkipSuccessOutput());
        $this->assertEquals('bar', $this->command->getMessage('foo'));
        $this->assertTrue($this->command->canRunInContext(
            m::mock('ClickNow\Checker\Command\CommandInterface'),
            m::mock('ClickNow\Checker\Context\ContextInterface')
        ));
    }

    public function testOverrideDefaultConfig()
    {
        $this->command->setConfig([
            'process_timeout'         => 60,
            'stop_on_failure'         => false,
            'ignore_unstaged_changes' => false,
            'skip_success_output'     => false,
            'message'                 => ['foo' => 'foo'],
            'can_run_in'              => false,
        ]);

        $this->assertEquals(60, $this->command->getProcessTimeout());
        $this->assertFalse($this->command->shouldStopOnFailure());
        $this->assertFalse($this->command->shouldIgnoreUnstagedChanges());
        $this->assertFalse($this->command->isSkipSuccessOutput());
        $this->assertEquals('foo', $this->command->getMessage('foo'));
        $this->assertFalse($this->command->canRunInContext(
            m::mock('ClickNow\Checker\Command\CommandInterface'),
            m::mock('ClickNow\Checker\Context\ContextInterface')
        ));
    }

    public function testIfCommandCanRunInContext()
    {
        $command = m::mock('ClickNow\Checker\Command\CommandInterface');
        $command->shouldReceive('getName')->andReturn('bar');

        $context = m::mock('ClickNow\Checker\Context\ContextInterface');
        $context->shouldReceive('getCommand')->andReturn($this->command);

        $this->command->setConfig(['can_run_in' => ['bar']]);
        $this->assertTrue($this->command->canRunInContext($command, $context));

        $this->command->setConfig(['can_run_in' => ['foo']]);
        $this->assertTrue($this->command->canRunInContext($command, $context));

        $this->command->setConfig(['can_run_in' => ['foobar']]);
        $this->assertFalse($this->command->canRunInContext($command, $context));
    }

    public function testDefaultActionMetadata()
    {
        $this->command->addAction($this->action, []);
        $metadata = $this->command->getActionMetadata($this->action);

        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertEquals(['priority' => 0, 'blocking' => true], $metadata);
        $this->assertEquals(0, $this->command->getPriorityAction($this->action));
        $this->assertTrue($this->command->isBlockingAction($this->action));
    }

    public function testOverrideDefaultActionMetadata()
    {
        $this->command->addAction($this->action, ['metadata' => ['blocking' => false]]);
        $metadata = $this->command->getActionMetadata($this->action);

        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertEquals(['priority' => 0, 'blocking' => false], $metadata);
        $this->assertEquals(0, $this->command->getPriorityAction($this->action));
        $this->assertFalse($this->command->isBlockingAction($this->action));
    }

    /**
     * @expectedException \ClickNow\Checker\Exception\ActionNotFoundException
     * @expectedExceptionMessage Action `bar` was not found.
     */
    public function testNotFoundActionMetadata()
    {
        $this->command->getActionMetadata($this->action);
    }

    public function testEmptyActionConfig()
    {
        $this->command->addAction($this->action);
        $config = $this->command->getActionConfig($this->action);
        $this->assertEmpty($config);
    }

    public function testActionConfig()
    {
        $this->command->addAction($this->action, ['foo' => 'bar']);
        $config = $this->command->getActionConfig($this->action);
        $this->assertEquals(['foo' => 'bar'], $config);
    }

    /**
     * @expectedException \ClickNow\Checker\Exception\ActionNotFoundException
     * @expectedExceptionMessage Action `bar` was not found.
     */
    public function testNotFoundActionConfig()
    {
        $this->command->getActionConfig($this->action);
    }

    public function testActionsToRun()
    {
        $action1 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action1->shouldReceive('getName')->andReturn('action1');
        $action1->shouldReceive('canRunInContext')->once()->andReturn(true);
        $this->command->addAction($action1);

        $action2 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action2->shouldReceive('getName')->andReturn('action2');
        $action2->shouldReceive('canRunInContext')->once()->andReturn(false);
        $this->command->addAction($action2);

        $action3 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action3->shouldReceive('getName')->andReturn('action3');
        $action3->shouldReceive('canRunInContext')->once()->andReturn(true);
        $this->command->addAction($action3);

        $context = m::mock('ClickNow\Checker\Context\ContextInterface');

        $result = $this->command->getActionsToRun($context);
        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(2, $result);

        $actions = $result->toArray();
        $this->assertSame($action1, $actions[0]);
        $this->assertSame($action3, $actions[1]);
    }

    /*public function testRun()
    {
        $resultSuccess = m::mock('ClickNow\Checker\Result\ResultInterface');
        $resultSuccess->shouldReceive('isSuccess')->andReturn(true);
        $resultSuccess->shouldReceive('isError')->andReturn(false);

        $resultError = m::mock('ClickNow\Checker\Result\ResultInterface');
        $resultError->shouldReceive('isSuccess')->andReturn(false);
        $resultError->shouldReceive('isError')->andReturn(true);

        $action1 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action1->shouldReceive('getName')->andReturn('action1');
        $action1->shouldReceive('canRunInContext')->once()->andReturn(true);
        $action1->shouldReceive('run')->once()->andReturn($resultSuccess);
        $this->command->addAction($action1);

        $action2 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action2->shouldReceive('getName')->andReturn('action2');
        $action2->shouldReceive('canRunInContext')->andReturn(true);
        $action2->shouldReceive('run')->once()->andReturn($resultError);
        $this->command->addAction($action2);

        $action3 = m::mock('ClickNow\Checker\Action\ActionInterface');
        $action3->shouldReceive('getName')->andReturn('action3');
        $action3->shouldReceive('canRunInContext')->andReturn(true);
        $action3->shouldReceive('run')->once()->andReturn($resultError);
        $this->command->addAction($action3);

        $command = m::mock('ClickNow\Checker\Command\CommandInterface');
        $context = m::mock('ClickNow\Checker\Context\ContextInterface');

        $this->command->run($command, $context);
    }*/
}
