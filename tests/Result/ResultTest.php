<?php

namespace ClickNow\Checker\Result;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Runner\ActionInterface;
use ClickNow\Checker\Runner\RunnerInterface;
use Mockery as m;

/**
 * @group  result
 * @covers \ClickNow\Checker\Result\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Runner\RunnerInterface|\Mockery\MockInterface
     */
    protected $runner;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \ClickNow\Checker\Runner\ActionInterface|\Mockery\MockInterface
     */
    protected $action;

    protected function setUp()
    {
        $this->runner = m::mock(RunnerInterface::class);
        $this->context = m::mock(ContextInterface::class);
        $this->action = m::mock(ActionInterface::class);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testSkipped()
    {
        $result = Result::skipped($this->runner, $this->context, $this->action);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(ResultInterface::SKIPPED, $result->getStatus());
        $this->assertInstanceOf(RunnerInterface::class, $result->getRunner());
        $this->assertSame($this->runner, $result->getRunner());
        $this->assertInstanceOf(ContextInterface::class, $result->getContext());
        $this->assertSame($this->context, $result->getContext());
        $this->assertInstanceOf(ActionInterface::class, $result->getAction());
        $this->assertSame($this->action, $result->getAction());
        $this->assertNull($result->getMessage());
        $this->assertTrue($result->isSkipped());
        $this->assertFalse($result->isSuccess());
        $this->assertFalse($result->isWarning());
        $this->assertFalse($result->isError());
    }

    public function testSuccess()
    {
        $result = Result::success($this->runner, $this->context, $this->action);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(ResultInterface::SUCCESS, $result->getStatus());
        $this->assertInstanceOf(RunnerInterface::class, $result->getRunner());
        $this->assertSame($this->runner, $result->getRunner());
        $this->assertInstanceOf(ContextInterface::class, $result->getContext());
        $this->assertSame($this->context, $result->getContext());
        $this->assertInstanceOf(ActionInterface::class, $result->getAction());
        $this->assertSame($this->action, $result->getAction());
        $this->assertNull($result->getMessage());
        $this->assertFalse($result->isSkipped());
        $this->assertTrue($result->isSuccess());
        $this->assertFalse($result->isWarning());
        $this->assertFalse($result->isError());
    }

    public function testWarning()
    {
        $result = Result::warning($this->runner, $this->context, $this->action, 'WARNING');

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(ResultInterface::WARNING, $result->getStatus());
        $this->assertInstanceOf(RunnerInterface::class, $result->getRunner());
        $this->assertSame($this->runner, $result->getRunner());
        $this->assertInstanceOf(ContextInterface::class, $result->getContext());
        $this->assertSame($this->context, $result->getContext());
        $this->assertInstanceOf(ActionInterface::class, $result->getAction());
        $this->assertSame($this->action, $result->getAction());
        $this->assertSame('WARNING', $result->getMessage());
        $this->assertFalse($result->isSkipped());
        $this->assertFalse($result->isSuccess());
        $this->assertTrue($result->isWarning());
        $this->assertFalse($result->isError());
    }

    public function testError()
    {
        $result = Result::error($this->runner, $this->context, $this->action, 'ERROR');

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(ResultInterface::ERROR, $result->getStatus());
        $this->assertInstanceOf(RunnerInterface::class, $result->getRunner());
        $this->assertSame($this->runner, $result->getRunner());
        $this->assertInstanceOf(ContextInterface::class, $result->getContext());
        $this->assertSame($this->context, $result->getContext());
        $this->assertInstanceOf(ActionInterface::class, $result->getAction());
        $this->assertSame($this->action, $result->getAction());
        $this->assertSame('ERROR', $result->getMessage());
        $this->assertFalse($result->isSkipped());
        $this->assertFalse($result->isSuccess());
        $this->assertFalse($result->isWarning());
        $this->assertTrue($result->isError());
    }
}
