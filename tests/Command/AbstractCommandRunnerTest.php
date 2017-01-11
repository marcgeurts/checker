<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\PlatformException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use Mockery as m;

/**
 * @group command
 * @covers \ClickNow\Checker\Command\AbstractCommandRunner
 */
class AbstractCommandRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\AbstractCommandRunner|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $command;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    protected function setUp()
    {
        $this->command = $this->getMockForAbstractClass(AbstractCommandRunner::class);
        $this->context = m::mock(ContextInterface::class);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(CommandInterface::class, $this->command);
    }

    public function testRunWithoutActions()
    {
        $this->command->expects($this->never())->method('isStopOnFailure');
        $this->command->expects($this->never())->method('isActionBlocking');

        $actions = new ActionsCollection();
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testRunAndReturnSuccess()
    {
        $this->command->expects($this->never())->method('isStopOnFailure');
        $this->command->expects($this->never())->method('isActionBlocking');

        $actions = new ActionsCollection([$this->mockAction(), $this->mockAction()]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testRunAndReturnAError()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();

        $result1 = Result::error($this->command, $this->context, $action1, 'ERROR');
        $action1->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result1);

        $this->command->expects($this->once())->method('isStopOnFailure')->willReturn(false);
        $this->command->expects($this->once())->method('isActionBlocking')->with($action1)->willReturn(true);

        $actions = new ActionsCollection([$action1, $action2]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR', $result->getMessage());
    }

    public function testRunAndReturnAWarning()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();

        $result1 = Result::warning($this->command, $this->context, $action1, 'WARNING');
        $action1->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result1);

        $this->command->expects($this->never())->method('isStopOnFailure');
        $this->command->expects($this->once())->method('isActionBlocking')->with($action1)->willReturn(true);

        $actions = new ActionsCollection([$action1, $action2]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertSame('WARNING', $result->getMessage());
    }

    public function testRunAndReturnSomeErrors()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();

        $result1 = Result::error($this->command, $this->context, $action1, 'ERROR1');
        $result2 = Result::error($this->command, $this->context, $action2, 'ERROR2');

        $action1->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result1);
        $action2->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result2);

        $this->command
            ->expects($this->exactly(2))
            ->method('isStopOnFailure')
            ->willReturn(false);

        $this->command
            ->expects($this->exactly(2))
            ->method('isActionBlocking')
            ->withConsecutive([$action1], [$action2])
            ->willReturn(true);

        $actions = new ActionsCollection([$action1, $action2]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR1'.PHP_EOL.'ERROR2', $result->getMessage());
    }

    public function testRunAndReturnSomeWarnings()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();
        $action3 = $this->mockAction();

        $result1 = Result::warning($this->command, $this->context, $action1, 'WARNING1');
        $result2 = Result::warning($this->command, $this->context, $action2, 'WARNING2');

        $action1->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result1);
        $action2->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result2);
        $action3
            ->shouldReceive('run')
            ->with($this->command, $this->context)
            ->once()
            ->andThrow(PlatformException::class, 'WARNING3');

        $this->command
            ->expects($this->never())
            ->method('isStopOnFailure');

        $this->command
            ->expects($this->exactly(3))
            ->method('isActionBlocking')
            ->withConsecutive([$action1], [$action2])
            ->willReturn(true);

        $actions = new ActionsCollection([$action1, $action2, $action3]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertSame('WARNING1'.PHP_EOL.'WARNING2'.PHP_EOL.'WARNING3', $result->getMessage());
    }

    public function testRunAndReturnSomeErrorsAndWarnings()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();
        $action3 = $this->mockAction();
        $action4 = $this->mockAction();

        $result1 = Result::error($this->command, $this->context, $action1, 'ERROR1');
        $result2 = Result::error($this->command, $this->context, $action2, 'ERROR2');
        $result3 = Result::warning($this->command, $this->context, $action3, 'WARNING1');
        $result4 = Result::warning($this->command, $this->context, $action4, 'WARNING2');

        $action1->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result1);
        $action2->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result2);
        $action3->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result3);
        $action4->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result4);

        $this->command
            ->expects($this->exactly(2))
            ->method('isStopOnFailure')
            ->willReturn(false);

        $this->command
            ->expects($this->exactly(4))
            ->method('isActionBlocking')
            ->withConsecutive([$action1], [$action2], [$action3], [$action4])
            ->willReturn(true);

        $actions = new ActionsCollection([$action1, $action2, $action3, $action4]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR1'.PHP_EOL.'ERROR2'.PHP_EOL.'WARNING1'.PHP_EOL.'WARNING2', $result->getMessage());
    }

    public function testRunWithStopOnFailure()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();

        $result1 = Result::error($this->command, $this->context, $action1, 'ERROR');

        $action1->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result1);
        $action2->shouldReceive('run')->with($this->command, $this->context)->never();

        $this->command->expects($this->once())->method('isStopOnFailure')->willReturn(true);
        $this->command->expects($this->once())->method('isActionBlocking')->with($action1)->willReturn(true);

        $actions = new ActionsCollection([$action1, $action2]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR', $result->getMessage());
    }

    public function testRunAndDoNotStopOnFailureIfTheActionIsNonABlocking()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();

        $result1 = Result::error($this->command, $this->context, $action1, 'ERROR');
        $result2 = Result::warning($this->command, $this->context, $action2, 'WARNING');

        $action1->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result1);
        $action2->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result2);

        $this->command
            ->expects($this->never())
            ->method('isStopOnFailure');

        $this->command
            ->expects($this->exactly(2))
            ->method('isActionBlocking')
            ->withConsecutive([$action1], [$action2])
            ->willReturn(false);

        $actions = new ActionsCollection([$action1, $action2]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertSame('ERROR'.PHP_EOL.'WARNING', $result->getMessage());
    }

    public function testRunAndValidatesTheReturnTypeOfAction()
    {
        $action1 = $this->mockAction();
        $action2 = $this->mockAction();

        $action1->shouldReceive('getName')->withNoArgs()->once()->andReturn('action1');
        $action2->shouldReceive('getName')->withNoArgs()->never();

        $action1
            ->shouldReceive('run')
            ->with($this->command, $this->context)
            ->once()
            ->andReturnNull();

        $action2
            ->shouldReceive('run')
            ->with($this->command, $this->context)
            ->once()
            ->andThrow(RuntimeException::class, 'ERROR');

        $this->command
            ->expects($this->exactly(2))
            ->method('isStopOnFailure')
            ->willReturn(false);

        $this->command
            ->expects($this->exactly(2))
            ->method('isActionBlocking')
            ->withConsecutive([$action1], [$action2])
            ->willReturn(true);

        $actions = new ActionsCollection([$action1, $action2]);
        $this->command->expects($this->once())->method('getActionsToRun')->willReturn($actions);
        $result = $this->command->run($this->command, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('Action `action1` did not return a Result.'.PHP_EOL.'ERROR', $result->getMessage());
    }

    /**
     * Mock action.
     *
     * @return \ClickNow\Checker\Action\ActionInterface|\Mockery\MockInterface
     */
    protected function mockAction()
    {
        $action = m::mock(ActionInterface::class);
        $result = Result::success($this->command, $this->context, $action);
        $action->shouldReceive('run')->with($this->command, $this->context)->once()->andReturn($result)->byDefault();

        return $action;
    }
}
