<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use Mockery as m;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\Command;
     */
    protected $command;

    protected function setUp()
    {
        $checker = m::mock(Checker::class);
        $checker->shouldReceive('getProcessTimeout')->zeroOrMoreTimes()->andReturn(null);
        $checker->shouldReceive('shouldStopOnFailure')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('shouldIgnoreUnstagedChanges')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('isSkipSuccessOutput')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturn(null);

        $this->command = new Command($checker, 'foo');
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testNameOfCommand()
    {
        $this->assertEquals('foo', $this->command->getName());
    }

    public function testEmptyActions()
    {
        $actions = $this->command->getActions();
        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertEmpty($actions);
    }

    public function testAddAction()
    {
        $action = $this->getAction();
        $this->command->addAction($action);
        $actions = $this->command->getActions();
        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertCount(1, $actions);
        $this->assertSame($action, $actions->first());
    }

    /**
     * @expectedException \ClickNow\Checker\Exception\ActionAlreadyRegisteredException
     * @expectedExceptionMessage Action `bar` already registered.
     */
    public function testAddActionThrowsWhenActionHasAlreadyBeenAdded()
    {
        $action = $this->getAction();
        $this->command->addAction($action);
        $this->command->addAction($action);
    }

    public function testConfigProcessTimeout()
    {
        $this->assertNull($this->command->getProcessTimeout());

        $this->command->setConfig(['process_timeout' => 60]);
        $this->assertEquals(60, $this->command->getProcessTimeout());

        $this->command->setConfig(['process_timeout' => 90.5]);
        $this->assertEquals(90.5, $this->command->getProcessTimeout());
    }

    public function testConfigStopOnFailure()
    {
        $this->assertFalse($this->command->shouldStopOnFailure());

        $this->command->setConfig(['stop_on_failure' => true]);
        $this->assertTrue($this->command->shouldStopOnFailure());
    }

    public function testConfigIgnoreUnstagedChanges()
    {
        $this->assertFalse($this->command->shouldIgnoreUnstagedChanges());

        $this->command->setConfig(['ignore_unstaged_changes' => true]);
        $this->assertTrue($this->command->shouldIgnoreUnstagedChanges());
    }

    public function testConfigSkipSuccessOutput()
    {
        $this->assertFalse($this->command->isSkipSuccessOutput());

        $this->command->setConfig(['skip_success_output' => true]);
        $this->assertTrue($this->command->isSkipSuccessOutput());
    }

    public function testConfigMessage()
    {
        $this->assertNull($this->command->getMessage('foo'));

        $this->command->setConfig(['message' => ['foo' => 'foo']]);
        $this->assertEquals('foo', $this->command->getMessage('foo'));
    }

    public function testConfigCanRunInContext()
    {
        $command = m::mock(CommandInterface::class);
        $context = m::mock(ContextInterface::class);

        $this->assertTrue($this->command->canRunInContext($command, $context));

        $this->command->setConfig(['can_run_in' => false]);
        $this->assertFalse($this->command->canRunInContext($command, $context));
    }

    public function testIfCommandCanRunInContext()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getName')->twice()->andReturn('bar');

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getCommand')->times(3)->andReturn($this->command);

        $this->command->setConfig(['can_run_in' => ['bar']]);
        $this->assertTrue($this->command->canRunInContext($command, $context));

        $this->command->setConfig(['can_run_in' => ['foo']]);
        $this->assertTrue($this->command->canRunInContext($command, $context));

        $this->command->setConfig(['can_run_in' => ['foobar']]);
        $this->assertFalse($this->command->canRunInContext($command, $context));
    }

    public function testDefaultActionMetadata()
    {
        $action = $this->getAction();
        $this->command->addAction($action);
        $metadata = $this->command->getActionMetadata($action);

        $this->assertInternalType('array', $metadata);
        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertEquals(['priority' => 0, 'blocking' => true], $metadata);
        $this->assertEquals(0, $this->command->getPriorityAction($action));
        $this->assertTrue($this->command->isBlockingAction($action));
    }

    public function testPriorityActionMetadata()
    {
        $action = $this->getAction();
        $this->command->addAction($action, ['metadata' => ['priority' => 100]]);
        $metadata = $this->command->getActionMetadata($action);

        $this->assertInternalType('array', $metadata);
        $this->assertEquals(['priority' => 100, 'blocking' => true], $metadata);
        $this->assertEquals(100, $this->command->getPriorityAction($action));
    }

    public function testBlockingActionMetadata()
    {
        $action = $this->getAction();
        $this->command->addAction($action, ['metadata' => ['blocking' => false]]);
        $metadata = $this->command->getActionMetadata($action);

        $this->assertInternalType('array', $metadata);
        $this->assertEquals(['priority' => 0, 'blocking' => false], $metadata);
        $this->assertFalse($this->command->isBlockingAction($action));
    }

    /**
     * @expectedException \ClickNow\Checker\Exception\ActionNotFoundException
     * @expectedExceptionMessage Action `bar` was not found.
     */
    public function testNotFoundActionMetadata()
    {
        $this->command->getActionMetadata($this->getAction());
    }

    public function testEmptyActionConfig()
    {
        $action = $this->getAction();
        $this->command->addAction($action);
        $config = $this->command->getActionConfig($action);

        $this->assertInternalType('array', $config);
        $this->assertEmpty($config);
    }

    public function testActionConfig()
    {
        $action = $this->getAction();
        $this->command->addAction($action, ['foo' => 'bar']);
        $config = $this->command->getActionConfig($action);

        $this->assertInternalType('array', $config);
        $this->assertEquals(['foo' => 'bar'], $config);
    }

    /**
     * @expectedException \ClickNow\Checker\Exception\ActionNotFoundException
     * @expectedExceptionMessage Action `bar` was not found.
     */
    public function testNotFoundActionConfig()
    {
        $this->command->getActionConfig($this->getAction());
    }

    public function testActionsToRun()
    {
        $action1 = m::mock(ActionInterface::class);
        $action1->shouldReceive('getName')->zeroOrMoreTimes()->andReturn('action1');
        $action1->shouldReceive('canRunInContext')->once()->andReturn(true);
        $this->command->addAction($action1);

        $action2 = m::mock(ActionInterface::class);
        $action2->shouldReceive('getName')->zeroOrMoreTimes()->andReturn('action2');
        $action2->shouldReceive('canRunInContext')->once()->andReturn(false);
        $this->command->addAction($action2);

        $action3 = m::mock(ActionInterface::class);
        $action3->shouldReceive('getName')->zeroOrMoreTimes()->andReturn('action3');
        $action3->shouldReceive('canRunInContext')->once()->andReturn(true);
        $this->command->addAction($action3);

        $context = m::mock(ContextInterface::class);

        $result = $this->command->getActionsToRun($context);
        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(2, $result);

        $actions = $result->toArray();
        $this->assertSame($action1, $actions[0]);
        $this->assertSame($action3, $actions[1]);
    }

    protected function getAction()
    {
        $action = m::mock(ActionInterface::class);
        $action->shouldReceive('getName')->zeroOrMoreTimes()->andReturn('bar');

        return $action;
    }
}
