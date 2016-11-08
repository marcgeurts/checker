<?php

namespace ClickNow\Checker\Action;

use ClickNow\Checker\Command\CommandInterface;
use ClickNow\Checker\Context\ContextInterface;
use Mockery as m;

/**
 * @group action
 * @covers \ClickNow\Checker\Action\ActionsCollection
 */
class ActionsCollectionTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testSortOnPriority()
    {
        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);
        $action3 = m::mock(ActionInterface::class);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getActionPriority')->once()->with($action1)->andReturn(100);
        $command->shouldReceive('getActionPriority')->once()->with($action2)->andReturn(200);
        $command->shouldReceive('getActionPriority')->once()->with($action3)->andReturn(100);

        $actionsCollection = new ActionsCollection([$action1, $action2, $action3]);
        $result = $actionsCollection->sortByPriority($command);
        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(3, $result);

        $actions = $result->toArray();
        $this->assertSame($action2, $actions[0]);
        $this->assertSame($action1, $actions[1]);
        $this->assertSame($action3, $actions[2]);
    }

    public function testFilterByContext()
    {
        $action1 = m::mock(ActionInterface::class);
        $action2 = m::mock(ActionInterface::class);

        $action1->shouldReceive('canRunInContext')->once()->andReturn(true);
        $action2->shouldReceive('canRunInContext')->once()->andReturn(false);

        $command = m::mock(CommandInterface::class);
        $context = m::mock(ContextInterface::class);

        $actionsCollection = new ActionsCollection([$action1, $action2]);
        $result = $actionsCollection->filterByContext($command, $context);
        $this->assertInstanceOf(ActionsCollection::class, $result);
        $this->assertCount(1, $result);

        $actions = $result->toArray();
        $this->assertSame($action1, $actions[0]);
    }
}
