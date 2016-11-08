<?php

namespace ClickNow\Checker\Event;

use ClickNow\Checker\Action\ActionsCollection;
use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Result\ResultsCollection;
use Mockery as m;
use Symfony\Component\EventDispatcher\Event;

/**
 * @group event
 * @covers \ClickNow\Checker\Event\RunnerEvent
 */
class RunnerEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Event\RunnerEvent
     */
    protected $event;

    protected function setUp()
    {
        $context = m::mock(ContextInterface::class);
        $actions = m::mock(ActionsCollection::class);
        $results = m::mock(ResultsCollection::class);

        $this->event = new RunnerEvent($context, $actions, $results);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Event::class, $this->event);
    }

    public function testGetContext()
    {
        $this->assertInstanceOf(ContextInterface::class, $this->event->getContext());
    }

    public function testGetActions()
    {
        $this->assertInstanceOf(ActionsCollection::class, $this->event->getActions());
    }

    public function testGetResults()
    {
        $this->assertInstanceOf(ResultsCollection::class, $this->event->getResults());
    }

    public function testGetResultsDefault()
    {
        $event = new RunnerEvent(m::mock(ContextInterface::class), m::mock(ActionsCollection::class));
        $this->assertNull($event->getResults());
    }
}
