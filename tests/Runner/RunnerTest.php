<?php

namespace ClickNow\Checker\Runner;

use ClickNow\Checker\Config\Checker;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Exception\PlatformException;
use ClickNow\Checker\Exception\RuntimeException;
use ClickNow\Checker\Result\Result;
use ClickNow\Checker\Result\ResultInterface;
use Mockery as m;

/**
 * @group  runner
 * @covers \ClickNow\Checker\Runner\Runner
 */
class RunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Runner\Runner
     */
    protected $runner;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    protected function setUp()
    {
        $this->runner = new Runner(m::spy(Checker::class), 'foo');
        $this->context = m::mock(ContextInterface::class);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(RunnerInterface::class, $this->runner);
    }

    public function testGetName()
    {
        $this->assertSame('foo', $this->runner->getName());
    }

    public function testGetActionsToRun()
    {
        $action1 = $this->mockAction('action1', null);
        $action2 = $this->mockAction('action2', null, null, false);
        $action3 = $this->mockAction('action3', null);

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);
        $this->runner->addAction($action3);

        $result = $this->runner->getActionsToRun($this->context);

        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame($action1, $result[0]);
        $this->assertSame($action3, $result[1]);
    }

    public function testRunWithoutActions()
    {
        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSkipped());
        $this->assertNull($result->getMessage());
    }

    public function testRunAndReturnSuccess()
    {
        $action1 = $this->mockAction('action1');
        $action2 = $this->mockAction('action2');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertNull($result->getMessage());
    }

    public function testRunAndReturnSkipped()
    {
        $action1 = $this->mockAction('action1', Result::SKIPPED);
        $action2 = $this->mockAction('action2', Result::SKIPPED);

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isSkipped());
        $this->assertNull($result->getMessage());
    }

    public function testRunAndReturnAError()
    {
        $action1 = $this->mockAction('action1', Result::ERROR, 'ERROR');
        $action2 = $this->mockAction('action2');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR', $result->getMessage());
    }

    public function testRunAndReturnAErrorWithStrict()
    {
        $action1 = $this->mockAction('action1', Result::WARNING, 'WARNING');
        $action2 = $this->mockAction('action2');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $this->runner->setStrict(true);
        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('WARNING', $result->getMessage());
    }

    public function testRunAndReturnAWarning()
    {
        $action1 = $this->mockAction('action1', Result::WARNING, 'WARNING');
        $action2 = $this->mockAction('action2');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertSame('WARNING', $result->getMessage());
    }

    public function testRunAndReturnSomeErrors()
    {
        $action1 = $this->mockAction('action1', Result::ERROR, 'ERROR1');
        $action2 = $this->mockAction('action2', Result::ERROR, 'ERROR2');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR1'.PHP_EOL.'ERROR2', $result->getMessage());
    }

    public function testRunAndReturnSomeWarnings()
    {
        $action1 = $this->mockAction('action1', Result::WARNING, 'WARNING1');
        $action2 = $this->mockAction('action2', Result::WARNING, 'WARNING2');
        $action3 = $this->mockAction('action3');

        $action3->shouldReceive('run')
            ->with($this->runner, $this->context)
            ->once()
            ->andThrow(PlatformException::class, 'WARNING3');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);
        $this->runner->addAction($action3);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertSame('WARNING1'.PHP_EOL.'WARNING2'.PHP_EOL.'WARNING3', $result->getMessage());
    }

    public function testRunAndReturnSomeErrorsAndWarnings()
    {
        $action1 = $this->mockAction('action1', Result::ERROR, 'ERROR1');
        $action2 = $this->mockAction('action2', Result::ERROR, 'ERROR2');
        $action3 = $this->mockAction('action3', Result::WARNING, 'WARNING1');
        $action4 = $this->mockAction('action4', Result::WARNING, 'WARNING2');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);
        $this->runner->addAction($action3);
        $this->runner->addAction($action4);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR1'.PHP_EOL.'ERROR2'.PHP_EOL.'WARNING1'.PHP_EOL.'WARNING2', $result->getMessage());
    }

    public function testRunWithStopOnFailure()
    {
        $action1 = $this->mockAction('action1', Result::ERROR, 'ERROR');
        $action2 = $this->mockAction('action2', null);

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $this->runner->setStopOnFailure(true);
        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('ERROR', $result->getMessage());
    }

    public function testRunAndDoNotStopOnFailureIfTheActionIsNonABlocking()
    {
        $action1 = $this->mockAction('action1', Result::ERROR, 'ERROR', true);
        $action2 = $this->mockAction('action2', Result::WARNING, 'WARNING', true);

        $this->runner->addAction($action1, ['metadata' => ['blocking' => false]]);
        $this->runner->addAction($action2);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isWarning());
        $this->assertSame('ERROR'.PHP_EOL.'WARNING', $result->getMessage());
    }

    public function testRunAndValidatesTheReturnTypeOfAction()
    {
        $action1 = $this->mockAction('action1');
        $action2 = $this->mockAction('action2');

        $action1->shouldReceive('run')->with($this->runner, $this->context)->once()->andReturnNull();
        $action2->shouldReceive('run')
            ->with($this->runner, $this->context)
            ->once()
            ->andThrow(RuntimeException::class, 'ERROR');

        $this->runner->addAction($action1);
        $this->runner->addAction($action2);

        $result = $this->runner->run($this->runner, $this->context);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertTrue($result->isError());
        $this->assertSame('Action `action1` did not return a Result.'.PHP_EOL.'ERROR', $result->getMessage());
    }

    /**
     * Mock action.
     *
     * @param string $name
     * @param int    $status
     * @param null   $message
     * @param bool   $canRunIn
     *
     * @return \ClickNow\Checker\Runner\ActionInterface|\Mockery\MockInterface
     */
    protected function mockAction($name, $status = Result::SUCCESS, $message = null, $canRunIn = true)
    {
        $action = m::mock(ActionInterface::class);
        $action->shouldReceive('getName')->withNoArgs()->atLeast()->once()->andReturn($name);
        $action->shouldReceive('canRunInContext')->with($this->runner, $this->context)->once()->andReturn($canRunIn);
        $action->shouldReceive('run')->with($this->runner, $this->context)->never()->byDefault();

        if (!is_null($status)) {
            $result = new Result($status, $this->runner, $this->context, $action, $message);
            $action->shouldReceive('run')->with($this->runner, $this->context)->once()->andReturn($result)->byDefault();
        }

        return $action;
    }
}
