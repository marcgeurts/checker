<?php

namespace ClickNow\Checker\Result;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use Mockery as m;

/**
 * @group result
 * @covers \ClickNow\Checker\Result\Result
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Command\CommandInterface|\Mockery\MockInterface
     */
    protected $command;

    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \ClickNow\Checker\Action\ActionInterface|\Mockery\MockInterface
     */
    protected $action;

    protected function setUp()
    {
        $this->command = m::mock(CommandInterface::class);
        $this->context = m::mock(ContextInterface::class);
        $this->action = m::mock(ActionInterface::class);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testSkipped()
    {
        $result = Result::skipped($this->command, $this->context, $this->action);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(Result::SKIPPED, $result->getStatus());
        $this->assertInstanceOf(CommandInterface::class, $result->getCommand());
        $this->assertSame($this->command, $result->getCommand());
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
        $result = Result::success($this->command, $this->context, $this->action);

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(Result::SUCCESS, $result->getStatus());
        $this->assertInstanceOf(CommandInterface::class, $result->getCommand());
        $this->assertSame($this->command, $result->getCommand());
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
        $result = Result::warning($this->command, $this->context, $this->action, 'WARNING');

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(Result::WARNING, $result->getStatus());
        $this->assertInstanceOf(CommandInterface::class, $result->getCommand());
        $this->assertSame($this->command, $result->getCommand());
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
        $result = Result::error($this->command, $this->context, $this->action, 'ERROR');

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame(Result::ERROR, $result->getStatus());
        $this->assertInstanceOf(CommandInterface::class, $result->getCommand());
        $this->assertSame($this->command, $result->getCommand());
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
