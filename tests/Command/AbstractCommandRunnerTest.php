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
    protected function tearDown()
    {
        m::close();
    }

    public function testRunsActionsSuccessfully()
    {
        list($command, $context) = $this->getRunners();

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testRunsActionsAndReturnAFail()
    {
        list($command, $context, $action1) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testRunsActionsAndReturnAWarning()
    {
        list($command, $context, $action1) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::warning($command, $context, $action1, 'WARNING'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('WARNING', $result->getMessage());
    }

    public function testRunsActionsAndReturnSomeFails()
    {
        list($command, $context, $action1, $action2) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR1'));
        $action2->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action2, 'ERROR2'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR1'.PHP_EOL.'ERROR2', $result->getMessage());
    }

    public function testRunsActionsAndReturnSomeWarnings()
    {
        list($command, $context, $action1, $action2) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::warning($command, $context, $action1, 'WARNING1'));
        $action2->shouldReceive('run')->once()->andReturn(Result::warning($command, $context, $action2, 'WARNING2'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('WARNING1'.PHP_EOL.'WARNING2', $result->getMessage());
    }

    public function testRunsActionsWithStopOnFailure()
    {
        list($command, $context, $action1, $action2) = $this->getRunners(['stop_on_failure' => true]);

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR'));
        $action2->shouldReceive('run')->never();

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testRunsActionsAndNotStopOnFailureIfTheActionIsNonABlocking()
    {
        list($command, $context, $action1) = $this->getRunners(
            ['stop_on_failure' => true],
            2,
            [['metadata' => ['blocking' => false]]]
        );

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testRunsActionsAndValidatesTheReturnTypeOfAction()
    {
        list($command, $context, $action1, $action2) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturnNull();
        $action2->shouldReceive('run')->once()->andThrow(RuntimeException::class, 'ERROR');

        $result = $command->run($command, $context);
        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('Action `action1` did not return a Result.'.PHP_EOL.'ERROR', $result->getMessage());
    }

    protected function getRunners(array $config = [], $numberOfActions = 2, array $configActions = [])
    {
        $checker = m::mock(Checker::class);
        $checker->shouldReceive('getProcessTimeout')->zeroOrMoreTimes()->andReturnNull();
        $checker->shouldReceive('shouldStopOnFailure')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('shouldIgnoreUnstagedChanges')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('isSkipSuccessOutput')->zeroOrMoreTimes()->andReturn(false);
        $checker->shouldReceive('getMessage')->zeroOrMoreTimes()->andReturnNull();

        $command = new Command($checker, 'foo');
        $command->setConfig($config);

        $context = m::mock(ContextInterface::class);
        $list = [$command, $context];

        for ($c = 0; $c < $numberOfActions; $c++) {
            $action = m::mock(ActionInterface::class);
            $action->shouldReceive('getName')->zeroOrMoreTimes()->andReturn('action'.($c + 1));
            $action->shouldReceive('canRunInContext')->once()->andReturn(true)->byDefault();
            $action->shouldReceive('run')->once()->andReturn(Result::success($command, $context, $action))->byDefault();
            $command->addAction($action, isset($configActions[$c]) ? $configActions[$c] : []);
            $list[] = $action;
        }

        return $list;
    }
}
