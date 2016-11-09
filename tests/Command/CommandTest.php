<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\ActionAlreadyRegisteredException;
use ClickNow\Checker\Exception\ActionNotFoundException;
use Mockery as m;

/**
 * @group command
 * @covers \ClickNow\Checker\Command\Command
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\Command;
     */
    protected $command;

    protected function setUp()
    {
        $checker = m::mock(Checker::class);
        $checker->shouldReceive('getProcessTimeout')->withNoArgs()->atLeast()->once()->andReturn(null);
        $checker->shouldReceive('isStopOnFailure')->withNoArgs()->atLeast()->once()->andReturn(false);
        $checker->shouldReceive('isIgnoreUnstagedChanges')->withNoArgs()->atLeast()->once()->andReturn(false);
        $checker->shouldReceive('isSkipSuccessOutput')->withNoArgs()->atLeast()->once()->andReturn(false);
        $checker->shouldReceive('getMessage')->withAnyArgs()->atMost()->once()->andReturn(null);

        $this->command = new Command($checker, 'foo');
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CommandInterface::class, $this->command);
        $this->assertInstanceOf(AbstractCommandRunner::class, $this->command);
    }

    public function testGetName()
    {
        $this->assertSame('foo', $this->command->getName());
    }

    public function testGetActionsIsEmpty()
    {
        $actions = $this->command->getActions();

        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertEmpty($actions);
    }

    public function testAddAction()
    {
        $action = $this->mockAction();
        $this->command->addAction($action);
        $actions = $this->command->getActions();

        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertCount(1, $actions);
        $this->assertSame($action, $actions->first());
    }

    public function testAddActionThrowsWhenActionHasAlreadyBeenAdded()
    {
        $this->setExpectedException(ActionAlreadyRegisteredException::class, 'Action `bar` already registered.');

        $action = $this->mockAction();
        $this->command->addAction($action);
        $this->command->addAction($action);
    }

    public function testGetProcessTimeout()
    {
        $this->assertNull($this->command->getProcessTimeout());
    }

    public function testSetProcessTimeoutToInteger()
    {
        $this->command->setConfig(['process_timeout' => 60]);
        $this->assertSame(60, $this->command->getProcessTimeout());
    }

    public function testSetProcessTimeoutToFloat()
    {
        $this->command->setConfig(['process_timeout' => 90.5]);
        $this->assertSame(90.5, $this->command->getProcessTimeout());
    }

    public function testStopOnFailureIsFalse()
    {
        $this->assertFalse($this->command->isStopOnFailure());
    }

    public function testStopOnFailureIsTrue()
    {
        $this->command->setConfig(['stop_on_failure' => true]);
        $this->assertTrue($this->command->isStopOnFailure());
    }

    public function testIgnoreUnstagedChangesIsFalse()
    {
        $this->assertFalse($this->command->isIgnoreUnstagedChanges());
    }

    public function testIgnoreUnstagedChangesIsTrue()
    {
        $this->command->setConfig(['ignore_unstaged_changes' => true]);
        $this->assertTrue($this->command->isIgnoreUnstagedChanges());
    }

    public function testSkipSuccessOutputIsFalse()
    {
        $this->assertFalse($this->command->isSkipSuccessOutput());
    }

    public function testSkipSuccessOutputIsTrue()
    {
        $this->command->setConfig(['skip_success_output' => true]);
        $this->assertTrue($this->command->isSkipSuccessOutput());
    }

    public function testGetMessage()
    {
        $this->assertNull($this->command->getMessage('foo'));
    }

    public function testSetMessage()
    {
        $this->command->setConfig(['message' => ['foo' => 'foo']]);
        $this->assertSame('foo', $this->command->getMessage('foo'));
    }

    public function testCanRunInContextIsTrue()
    {
        $command = m::mock(CommandInterface::class);
        $context = m::mock(ContextInterface::class);

        $this->assertTrue($this->command->canRunInContext($command, $context));
    }

    public function testCanRunInContextIsFalse()
    {
        $command = m::mock(CommandInterface::class);
        $context = m::mock(ContextInterface::class);

        $this->command->setConfig(['can_run_in' => false]);
        $this->assertFalse($this->command->canRunInContext($command, $context));
    }

    public function testCanRunInContextByArray()
    {
        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getName')->withNoArgs()->twice()->andReturn('bar');

        $context = m::mock(ContextInterface::class);
        $context->shouldReceive('getCommand')->withNoArgs()->times(3)->andReturn($this->command);

        $this->command->setConfig(['can_run_in' => ['bar']]);
        $this->assertTrue($this->command->canRunInContext($command, $context));

        $this->command->setConfig(['can_run_in' => ['foo']]);
        $this->assertTrue($this->command->canRunInContext($command, $context));

        $this->command->setConfig(['can_run_in' => ['foobar']]);
        $this->assertFalse($this->command->canRunInContext($command, $context));
    }

    public function testGetActionMetadataDefault()
    {
        $action = $this->mockAction();
        $this->command->addAction($action);
        $metadata = $this->command->getActionMetadata($action);

        $this->assertInternalType('array', $metadata);
        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertSame(['priority' => 0, 'blocking' => true], $metadata);
        $this->assertSame(0, $this->command->getActionPriority($action));
        $this->assertTrue($this->command->isActionBlocking($action));
    }

    public function testGetActionMetadataNotFound()
    {
        $this->setExpectedException(ActionNotFoundException::class, 'Action `bar` was not found.');

        $this->command->getActionMetadata($this->mockAction());
    }

    public function testSetActionMetadataPriority()
    {
        $action = $this->mockAction();
        $this->command->addAction($action, ['metadata' => ['priority' => 100]]);
        $metadata = $this->command->getActionMetadata($action);

        $this->assertInternalType('array', $metadata);
        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertSame(['priority' => 100, 'blocking' => true], $metadata);
        $this->assertSame(100, $this->command->getActionPriority($action));
        $this->assertTrue($this->command->isActionBlocking($action));
    }

    public function testSetActionMetadataBlocking()
    {
        $action = $this->mockAction();
        $this->command->addAction($action, ['metadata' => ['blocking' => false]]);
        $metadata = $this->command->getActionMetadata($action);

        $this->assertInternalType('array', $metadata);
        $this->assertCount(2, $metadata);
        $this->assertArrayHasKey('priority', $metadata);
        $this->assertArrayHasKey('blocking', $metadata);
        $this->assertSame(['priority' => 0, 'blocking' => false], $metadata);
        $this->assertSame(0, $this->command->getActionPriority($action));
        $this->assertFalse($this->command->isActionBlocking($action));
    }

    public function testGetActionConfig()
    {
        $action = $this->mockAction();
        $this->command->addAction($action, ['foo' => 'bar']);
        $config = $this->command->getActionConfig($action);

        $this->assertInternalType('array', $config);
        $this->assertSame(['foo' => 'bar'], $config);
    }

    public function testGetActionConfigEmpty()
    {
        $action = $this->mockAction();
        $this->command->addAction($action);
        $config = $this->command->getActionConfig($action);

        $this->assertInternalType('array', $config);
        $this->assertEmpty($config);
    }

    public function testGetActionConfigNotFound()
    {
        $this->setExpectedException(ActionNotFoundException::class, 'Action `bar` was not found.');

        $this->command->getActionConfig($this->mockAction());
    }

    public function testGetActionsToRun()
    {
        $context = m::mock(ContextInterface::class);

        $action1 = m::mock(ActionInterface::class);
        $action1->shouldReceive('getName')->withNoArgs()->atLeast()->once()->andReturn('action1');
        $action1->shouldReceive('canRunInContext')->with($this->command, $context)->once()->andReturn(true);
        $this->command->addAction($action1);

        $action2 = m::mock(ActionInterface::class);
        $action2->shouldReceive('getName')->withNoArgs()->atLeast()->once()->andReturn('action2');
        $action2->shouldReceive('canRunInContext')->with($this->command, $context)->once()->andReturn(false);
        $this->command->addAction($action2);

        $action3 = m::mock(ActionInterface::class);
        $action3->shouldReceive('getName')->withNoArgs()->atLeast()->once()->andReturn('action3');
        $action3->shouldReceive('canRunInContext')->with($this->command, $context)->once()->andReturn(true);
        $this->command->addAction($action3);

        $result = $this->command->getActionsToRun($context);
        $actions = $result->toArray();

        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($action1, $actions[0]);
        $this->assertSame($action3, $actions[1]);
    }

    /**
     * @return \ClickNow\Checker\Action\ActionInterface|\Mockery\MockInterface
     */
    protected function mockAction()
    {
        $action = m::mock(ActionInterface::class);
        $action->shouldReceive('getName')->withNoArgs()->atLeast()->once()->andReturn('bar');

        return $action;
    }
}
