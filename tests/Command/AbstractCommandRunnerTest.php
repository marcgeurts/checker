<?php

namespace ClickNow\Checker\Command;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use Mockery as m;

class AbstractCommandRunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\Command;
     */
    protected $command;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    protected function setUp()
    {
        $checker = m::mock(Checker::class);
        $checker->shouldReceive('getProcessTimeout')->zeroOrMoreTimes()->andReturnNull();
        $checker->shouldReceive('shouldStopOnFailure')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('shouldIgnoreUnstagedChanges')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('isSkipSuccessOutput')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturnNull();

        $this->command = new Command($checker, 'foo');
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

    public function testWithoutActions()
    {
        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testReturnSuccess()
    {
        $this->command->addAction($this->createAction('action1'));
        $this->command->addAction($this->createAction('action2'));

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testReturnAError()
    {
        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturn(
            Result::error($this->command, $this->context, $action1, 'ERROR')
        );

        $this->command->addAction($action1);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testReturnAWarning()
    {
        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturn(
            Result::warning($this->command, $this->context, $action1, 'WARNING')
        );

        $this->command->addAction($action1);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('WARNING', $result->getMessage());
    }

    public function testReturnSomeErrors()
    {
        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturn(
            Result::error($this->command, $this->context, $action1, 'ERROR1')
        );

        $action2->shouldReceive('run')->once()->andReturn(
            Result::error($this->command, $this->context, $action2, 'ERROR2')
        );

        $this->command->addAction($action1);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR1'.PHP_EOL.'ERROR2', $result->getMessage());
    }

    public function testReturnSomeWarnings()
    {
        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturn(
            Result::warning($this->command, $this->context, $action1, 'WARNING1')
        );

        $action2->shouldReceive('run')->once()->andReturn(
            Result::warning($this->command, $this->context, $action2, 'WARNING2')
        );

        $this->command->addAction($action1);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('WARNING1'.PHP_EOL.'WARNING2', $result->getMessage());
    }

    public function testReturnSomeErrorsAndWarnings()
    {
        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturn(
            Result::warning($this->command, $this->context, $action1, 'ERROR')
        );

        $action2->shouldReceive('run')->once()->andReturn(
            Result::error($this->command, $this->context, $action2, 'WARNING')
        );

        $this->command->addAction($action1);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR'.PHP_EOL.'WARNING', $result->getMessage());
    }

    public function testWithStopOnFailure()
    {
        $this->command->setConfig(['stop_on_failure' => true]);

        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturn(
            Result::error($this->command, $this->context, $action1, 'ERROR')
        );

        $action2->shouldReceive('run')->never();

        $this->command->addAction($action1);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testDoNotStopOnFailureIfTheActionIsNonABlocking()
    {
        $this->command->setConfig(['stop_on_failure' => true]);

        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturn(
            Result::error($this->command, $this->context, $action1, 'ERROR')
        );

        $action2->shouldReceive('run')->once()->andReturn(
            Result::warning($this->command, $this->context, $action2, 'WARNING')
        );

        $this->command->addAction($action1, ['metadata' => ['blocking' => false]]);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('ERROR'.PHP_EOL.'WARNING', $result->getMessage());
    }

    public function testValidatesTheReturnTypeOfAction()
    {
        $action1 = $this->createAction('action1');
        $action2 = $this->createAction('action2');

        $action1->shouldReceive('run')->once()->andReturnNull();
        $action2->shouldReceive('run')->once()->andThrow(RuntimeException::class, 'ERROR');

        $this->command->addAction($action1);
        $this->command->addAction($action2);

        $result = $this->command->run($this->command, $this->context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('Action `action1` did not return a Result.'.PHP_EOL.'ERROR', $result->getMessage());
    }

    /**
     * @return \ClickNow\Checker\Action\ActionInterface|\Mockery\MockInterface
     */
    protected function createAction($name)
    {
        $action = m::mock(ActionInterface::class);
        $action->shouldReceive('getName')->zeroOrMoreTimes()->andReturn($name);
        $action->shouldReceive('canRunInContext')->once()->andReturn(true)->byDefault();
        $action->shouldReceive('run')->once()->andReturn(
            Result::success($this->command, $this->context, $action)
        )->byDefault();

        return $action;
    }
}
