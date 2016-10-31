<?php

use ClickNow\Checker\Command\Command;
use ClickNow\Checker\Result\Result;
use Mockery as m;

class AbstractCommandRunnerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRunsActionsSuccessfully()
    {
        list($command, $context) = $this->getRunners();

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testRunsActionsAndReturnAFail()
    {
        list($command, $context, $action1) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testRunsActionsAndReturnAWarning()
    {
        list($command, $context, $action1) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::warning($command, $context, $action1, 'WARNING'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('WARNING', $result->getMessage());
    }

    public function testRunsActionsAndReturnSomeFails()
    {
        list($command, $context, $action1, $action2) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR1'));
        $action2->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action2, 'ERROR2'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR1'.PHP_EOL.'ERROR2', $result->getMessage());
    }

    public function testRunsActionsAndReturnSomeWarnings()
    {
        list($command, $context, $action1, $action2) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturn(Result::warning($command, $context, $action1, 'WARNING1'));
        $action2->shouldReceive('run')->once()->andReturn(Result::warning($command, $context, $action2, 'WARNING2'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('WARNING1'.PHP_EOL.'WARNING2', $result->getMessage());
    }

    public function testRunsActionsWithStopOnFailure()
    {
        list($command, $context, $action1, $action2) = $this->getRunners(['stop_on_failure' => true]);

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR'));
        $action2->shouldReceive('run')->never();

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testRunsActionsAndNotStopOnFailureIfTheActionIsNonABlocking()
    {
        list($command, $context, $action1, $action2) = $this->getRunners(
            ['stop_on_failure' => true],
            2,
            [['metadata' => ['blocking' => false]]]
        );

        $action1->shouldReceive('run')->once()->andReturn(Result::error($command, $context, $action1, 'ERROR'));

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isWarning());
        $this->assertEquals('ERROR', $result->getMessage());
    }

    public function testRunsActionsAndValidatesTheReturnTypeOfAction()
    {
        list($command, $context, $action1, $action2) = $this->getRunners();

        $action1->shouldReceive('run')->once()->andReturnNull();
        $action2->shouldReceive('run')->once()->andThrow(\ClickNow\Checker\Exception\RuntimeException::class, 'ERROR');

        $result = $command->run($command, $context);
        $this->assertInstanceOf('ClickNow\Checker\Result\ResultInterface', $result);
        $this->assertTrue($result->isError());
        $this->assertEquals('Action `action1` did not return a Result.'.PHP_EOL.'ERROR', $result->getMessage());
    }

    public function getRunners(array $config = [], $numberOfActions = 2, array $configActions = [])
    {
        $checker = m::mock('ClickNow\Checker\Config\Checker');
        $checker->shouldReceive('getProcessTimeout')->andReturnNull();
        $checker->shouldReceive('shouldStopOnFailure')->andReturn(false);
        $checker->shouldReceive('shouldIgnoreUnstagedChanges')->andReturn(false);
        $checker->shouldReceive('isSkipSuccessOutput')->andReturn(false);
        $checker->shouldReceive('getMessage')->andReturnNull();

        $command = new Command($checker, 'foo');
        $command->setConfig($config);

        $context = m::mock('ClickNow\Checker\Context\ContextInterface');
        $list = [$command, $context];

        for ($c = 0; $c < $numberOfActions; $c++) {
            $action = m::mock('ClickNow\Checker\Action\ActionInterface');
            $action->shouldReceive('getName')->andReturn('action'.($c + 1));
            $action->shouldReceive('canRunInContext')->once()->andReturn(true)->byDefault();
            $action->shouldReceive('run')->once()->andReturn(Result::success($command, $context, $action))->byDefault();
            $command->addAction($action, isset($configActions[$c]) ? $configActions[$c] : []);
            $list[] = $action;
        }

        return $list;
    }
}
