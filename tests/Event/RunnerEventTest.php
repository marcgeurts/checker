<?php

namespace ClickNow\Checker\Event;

use ClickNow\Checker\Context\ContextInterface;
use ClickNow\Checker\Result\ResultsCollection;
use ClickNow\Checker\Runner\ActionsCollection;
use Mockery as m;
use Symfony\Component\EventDispatcher\Event;

/**
 * @group  event
 * @covers \ClickNow\Checker\Event\RunnerEvent
 */
class RunnerEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Context\ContextInterface|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \ClickNow\Checker\Runner\ActionsCollection|\Mockery\MockInterface
     */
    protected $actions;

    /**
     * @var \ClickNow\Checker\Result\ResultsCollection|\Mockery\MockInterface
     */
    protected $results;

    /**
     * @var \ClickNow\Checker\Event\RunnerEvent
     */
    protected $runnerEvent;

    protected function setUp()
    {
        $this->context = m::mock(ContextInterface::class);
        $this->actions = m::mock(ActionsCollection::class);
        $this->results = m::mock(ResultsCollection::class);
        $this->runnerEvent = new RunnerEvent($this->context, $this->actions, $this->results);
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Event::class, $this->runnerEvent);
    }

    public function testGetContext()
    {
        $context = $this->runnerEvent->getContext();

        $this->assertInstanceOf(ContextInterface::class, $context);
        $this->assertSame($this->context, $context);
    }

    public function testGetActions()
    {
        $actions = $this->runnerEvent->getActions();

        $this->assertInstanceOf(ActionsCollection::class, $actions);
        $this->assertSame($this->actions, $actions);
    }

    public function testGetResults()
    {
        $results = $this->runnerEvent->getResults();

        $this->assertInstanceOf(ResultsCollection::class, $results);
        $this->assertSame($this->results, $results);
    }

    public function testGetResultsDefault()
    {
        $runnerEvent = new RunnerEvent($this->context, $this->actions);
        $this->assertNull($runnerEvent->getResults());
    }
}
