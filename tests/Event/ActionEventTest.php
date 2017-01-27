<?php

namespace ClickNow\Checker\Event;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Result\ResultInterface;
use ClickNow\Checker\Runner\ActionInterface;
use Mockery as m;
use Symfony\Component\EventDispatcher\Event;

/**
 * @group  event
 * @covers \ClickNow\Checker\Event\ActionEvent
 */
class ActionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \ClickNow\Checker\Runner\ActionInterface|\Mockery\MockInterface
     */
    protected $action;

    /**
     * @var \ClickNow\Checker\Result\ResultInterface|\Mockery\MockInterface
     */
    protected $result;

    /**
     * @var \ClickNow\Checker\Event\ActionEvent
     */
    protected $actionEvent;

    protected function setUp()
    {
        $this->context = m::mock(ContextInterface::class);
        $this->action = m::mock(ActionInterface::class);
        $this->result = m::mock(ResultInterface::class);
        $this->actionEvent = new ActionEvent($this->context, $this->action, $this->result);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Event::class, $this->actionEvent);
    }

    public function testGetContext()
    {
        $context = $this->actionEvent->getContext();

        $this->assertInstanceOf(ContextInterface::class, $context);
        $this->assertSame($this->context, $context);
    }

    public function testGetAction()
    {
        $action = $this->actionEvent->getAction();

        $this->assertInstanceOf(ActionInterface::class, $action);
        $this->assertSame($this->action, $action);
    }

    public function testGetResult()
    {
        $result = $this->actionEvent->getResult();

        $this->assertInstanceOf(ResultInterface::class, $result);
        $this->assertSame($this->result, $result);
    }

    public function testGetResultDefault()
    {
        $actionEvent = new ActionEvent($this->context, $this->action);
        $this->assertNull($actionEvent->getResult());
    }
}
