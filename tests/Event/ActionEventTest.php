<?php

namespace ClickNow\Checker\Event;

use ClickNow\Checker\Action\ActionInterface;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Result\ResultInterface;
use Mockery as m;
use Symfony\Component\EventDispatcher\Event;

/**
 * @group event
 * @covers \ClickNow\Checker\Event\ActionEvent
 */
class ActionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Event\ActionEvent
     */
    protected $actionEvent;

    protected function setUp()
    {
        $context = m::mock(ContextInterface::class);
        $action = m::mock(ActionInterface::class);
        $result = m::mock(ResultInterface::class);

        $this->actionEvent = new ActionEvent($context, $action, $result);
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
        $this->assertInstanceOf(ContextInterface::class, $this->actionEvent->getContext());
    }

    public function testGetAction()
    {
        $this->assertInstanceOf(ActionInterface::class, $this->actionEvent->getAction());
    }

    public function testGetResult()
    {
        $this->assertInstanceOf(ResultInterface::class, $this->actionEvent->getResult());
    }

    public function testGetResultDefault()
    {
        $actionEvent = new ActionEvent(m::mock(ContextInterface::class), m::mock(ActionInterface::class));

        $this->assertNull($actionEvent->getResult());
    }
}
