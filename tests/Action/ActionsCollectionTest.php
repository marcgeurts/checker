<?php

namespace ClickNow\Checker\Action;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * @group action
 * @covers \ClickNow\Checker\Action\ActionsCollection
 */
class ActionsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \ClickNow\Checker\Action\ActionsCollection
     */
    protected $actionsCollection;

    protected function setUp()
    {
        $this->actionsCollection = new ActionsCollection();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->actionsCollection);
    }

    public function testSortOnPriority()
    {
        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);
        $action3 = m::mock(ActionInterface::class);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionPriority')->with($action1)->once()->andReturn(100)->ordered();
        $command->shouldReceive('getActionPriority')->with($action2)->once()->andReturn(200)->ordered();
        $command->shouldReceive('getActionPriority')->with($action3)->once()->andReturn(100)->ordered();

        $this->actionsCollection->add($action1);
        $this->actionsCollection->add($action2);
        $this->actionsCollection->add($action3);

        $result = $this->actionsCollection->sortByPriority($command);
        $actions = $result->toArray();

        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(3, $result);
        $this->assertSame($action2, $actions[0]);
        $this->assertSame($action1, $actions[1]);
        $this->assertSame($action3, $actions[2]);
    }

    public function testFilterByContext()
    {
        $command = m::mock(CommandInterface::class);
        $context = m::mock(ContextInterface::class);

        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);

        $action1->shouldReceive('canRunInContext')->with($command, $context)->once()->andReturn(true);
        $action2->shouldReceive('canRunInContext')->with($command, $context)->once()->andReturn(false);

        $this->actionsCollection->add($action1);
        $this->actionsCollection->add($action2);

        $result = $this->actionsCollection->filterByContext($command, $context);
        $actions = $result->toArray();

        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertSame($action1, $actions[0]);
    }
}
